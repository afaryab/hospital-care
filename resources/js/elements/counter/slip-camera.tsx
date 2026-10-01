import {
    slipPhotoStore,
    slipPhotoStoreUnassigned,
    slipPhotoUpdate,
} from '@/routes';
import { clsx } from 'clsx';
import { Camera, CameraOff, LoaderCircle, RotateCcw } from 'lucide-react';
import {
    createContext,
    useCallback,
    useContext,
    useEffect,
    useRef,
    useState,
    type ReactNode,
} from 'react';

export type SlipPhotoSubject = 'patient' | 'guardian';

export type SlipPhotoSummary = {
    id: number;
    subject: SlipPhotoSubject;
    subject_label: string;
    source: 'manual' | 'auto';
    captured_by: string | null;
    captured_at: string | null;
    url: string;
};

type SlipPatient = {
    year: number | string;
    month: number | string;
    number: number | string;
};

type SlipCameraContextValue = {
    /**
     * Makes sure the patient has a pending slip photo before a slip is
     * generated: snaps one automatically when the camera is live and none
     * was taken. Never throws — a missing camera must not block billing.
     */
    ensurePhoto: () => Promise<void>;
};

const SlipCameraContext = createContext<SlipCameraContextValue>({
    ensurePhoto: async () => {},
});

export const useSlipCamera = () => useContext(SlipCameraContext);

const DEVICE_STORAGE_KEY = 'slip-camera-device';
const UPLOAD_TIMEOUT_MS = 8000;

const cameraSupported = () =>
    typeof window !== 'undefined' &&
    window.isSecureContext &&
    !!navigator.mediaDevices?.getUserMedia;

const readStoredDevice = (): string => {
    try {
        return localStorage.getItem(DEVICE_STORAGE_KEY) ?? '';
    } catch {
        return '';
    }
};

const storeDevice = (deviceId: string) => {
    try {
        localStorage.setItem(DEVICE_STORAGE_KEY, deviceId);
    } catch {
        // Per-viewer convenience only.
    }
};

const xsrfToken = () =>
    decodeURIComponent(
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1] ?? '',
    );

const sendJson = (url: string, method: string, body: FormData | string) =>
    fetch(url, {
        method,
        body,
        credentials: 'include',
        signal: AbortSignal.timeout(UPLOAD_TIMEOUT_MS),
        headers: {
            Accept: 'application/json',
            'X-XSRF-TOKEN': xsrfToken(),
            ...(typeof body === 'string'
                ? { 'Content-Type': 'application/json' }
                : {}),
        },
    });

export function SlipCameraProvider({
    patient,
    pendingSlipPhoto,
    children,
}: {
    patient?: SlipPatient | null;
    pendingSlipPhoto?: SlipPhotoSummary | null;
    children: ReactNode;
}) {
    const videoRef = useRef<HTMLVideoElement>(null);
    const streamRef = useRef<MediaStream | null>(null);
    const autoTriedRef = useRef(false);

    const [cameraOn, setCameraOn] = useState(false);
    const [cameraError, setCameraError] = useState<string | null>(null);
    const [devices, setDevices] = useState<MediaDeviceInfo[]>([]);
    const [deviceId, setDeviceId] = useState<string>('');
    const [subject, setSubject] = useState<SlipPhotoSubject>(
        pendingSlipPhoto?.subject ?? 'patient',
    );
    const [pending, setPending] = useState<SlipPhotoSummary | null>(
        pendingSlipPhoto ?? null,
    );
    const [busy, setBusy] = useState(false);
    // Starts false so server and first client render match; the panel stays
    // hidden on origins where the browser blocks webcams (plain http).
    const [supported, setSupported] = useState(false);
    const [uploadError, setUploadError] = useState<string | null>(null);

    const pendingRef = useRef(pending);
    pendingRef.current = pending;

    const stopCamera = useCallback(() => {
        streamRef.current?.getTracks().forEach((track) => track.stop());
        streamRef.current = null;
        setCameraOn(false);
    }, []);

    const startCamera = useCallback(
        async (selectedDeviceId?: string) => {
            setCameraError(null);
            stopCamera();

            if (!cameraSupported()) {
                setCameraError(
                    'Webcam unavailable: the browser only allows it over HTTPS or on localhost. Slips will be saved without a photo.',
                );
                return;
            }

            const constraints = (id?: string): MediaStreamConstraints => ({
                video: id
                    ? { deviceId: { exact: id } }
                    : { facingMode: 'user' },
                audio: false,
            });

            try {
                let stream: MediaStream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia(
                        constraints(selectedDeviceId),
                    );
                } catch (e) {
                    // A remembered camera may have been unplugged.
                    if (
                        selectedDeviceId &&
                        e instanceof DOMException &&
                        (e.name === 'OverconstrainedError' ||
                            e.name === 'NotFoundError')
                    ) {
                        stream =
                            await navigator.mediaDevices.getUserMedia(
                                constraints(),
                            );
                    } else {
                        throw e;
                    }
                }

                streamRef.current = stream;

                if (videoRef.current) {
                    videoRef.current.srcObject = stream;
                    await videoRef.current.play();
                }
                setCameraOn(true);

                const activeId =
                    stream.getVideoTracks()[0]?.getSettings().deviceId ?? '';
                setDeviceId(activeId);
                storeDevice(activeId);

                const allDevices =
                    await navigator.mediaDevices.enumerateDevices();
                setDevices(allDevices.filter((d) => d.kind === 'videoinput'));
            } catch (e) {
                const name = e instanceof DOMException ? e.name : '';
                setCameraError(
                    name === 'NotAllowedError'
                        ? 'Camera permission was denied. Allow camera access in the browser — slips are saved without a photo until then.'
                        : name === 'NotFoundError'
                          ? 'No webcam found. Check the camera is attached — slips are saved without a photo until then.'
                          : 'The camera could not be started (it may be in use by another application).',
                );
                stopCamera();
            }
        },
        [stopCamera],
    );

    useEffect(() => {
        if (!cameraSupported()) {
            return;
        }
        setSupported(true);
        startCamera(readStoredDevice() || undefined);
        return stopCamera;
    }, [startCamera, stopCamera]);

    const grabFrame = useCallback(
        (): Promise<Blob | null> =>
            new Promise((resolve) => {
                const video = videoRef.current;
                if (!video || !video.videoWidth || !streamRef.current) {
                    resolve(null);
                    return;
                }
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d')?.drawImage(video, 0, 0);
                canvas.toBlob(resolve, 'image/jpeg', 0.85);
            }),
        [],
    );

    const capture = useCallback(
        async (source: 'manual' | 'auto') => {
            // Auto-capture needs a patient; a manual snap before one is
            // chosen is held at the counter and claimed on patient selection.
            if (!patient && source === 'auto') {
                return;
            }
            const blob = await grabFrame();
            if (!blob) {
                return;
            }

            const body = new FormData();
            body.append(
                'photo',
                new File([blob], 'slip-photo.jpg', { type: 'image/jpeg' }),
            );
            body.append('subject', subject);
            body.append('source', source);

            setBusy(true);
            setUploadError(null);
            try {
                const response = await sendJson(
                    patient
                        ? slipPhotoStore({
                              year: patient.year,
                              month: patient.month,
                              number: patient.number,
                          }).url
                        : slipPhotoStoreUnassigned().url,
                    'POST',
                    body,
                );
                const json = await response.json().catch(() => ({}));
                if (response.ok) {
                    setPending(json.data);
                } else {
                    setUploadError(
                        json.message ?? 'The photo could not be saved.',
                    );
                }
            } catch {
                setUploadError(
                    'The photo could not be saved (network timeout).',
                );
            } finally {
                setBusy(false);
            }
        },
        [grabFrame, patient, subject],
    );

    // Auto-capture once the camera is live for a newly selected/created
    // patient that has no pending photo yet at this counter.
    useEffect(() => {
        if (patient && cameraOn && !pending && !autoTriedRef.current) {
            autoTriedRef.current = true;
            const timer = window.setTimeout(() => capture('auto'), 800);
            return () => window.clearTimeout(timer);
        }
    }, [patient, cameraOn, pending, capture]);

    const changeSubject = async (next: SlipPhotoSubject) => {
        setSubject(next);
        const current = pendingRef.current;
        if (!current || current.subject === next) {
            return;
        }
        try {
            const response = await sendJson(
                slipPhotoUpdate({ slipPhoto: current.id }).url,
                'PATCH',
                JSON.stringify({ subject: next }),
            );
            if (response.ok) {
                setPending((await response.json()).data);
            }
        } catch {
            setUploadError('Could not update the photo label.');
        }
    };

    const ensurePhoto = useCallback(async () => {
        if (!pendingRef.current && patient && streamRef.current) {
            await capture('auto');
        }
    }, [capture, patient]);

    return (
        <SlipCameraContext.Provider value={{ ensurePhoto }}>
            <div
                className={clsx(
                    'flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-2 dark:border-neutral-800 dark:bg-neutral-900',
                    !supported && 'hidden',
                )}
            >
                <div className="relative flex h-24 w-32 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-neutral-900">
                    <video
                        ref={videoRef}
                        muted
                        playsInline
                        aria-label="Live webcam"
                        className={clsx(
                            'h-full w-full -scale-x-100 object-cover',
                            !cameraOn && 'hidden',
                        )}
                    />
                    {!cameraOn && (
                        <CameraOff className="h-6 w-6 text-neutral-500" />
                    )}
                </div>

                <div className="flex min-w-48 flex-1 flex-col gap-2">
                    <div
                        role="radiogroup"
                        aria-label="Photo is of"
                        className="inline-flex w-fit rounded-lg border border-slate-200 bg-white p-0.5 text-xs font-medium dark:border-neutral-700 dark:bg-neutral-950"
                    >
                        {(['patient', 'guardian'] as const).map((value) => (
                            <button
                                key={value}
                                type="button"
                                role="radio"
                                aria-checked={subject === value}
                                onClick={() => changeSubject(value)}
                                className={clsx(
                                    'rounded-md px-3 py-1 capitalize transition-colors',
                                    subject === value
                                        ? 'bg-slate-800 text-white dark:bg-neutral-100 dark:text-neutral-900'
                                        : 'text-slate-600 hover:bg-slate-100 dark:text-neutral-300 dark:hover:bg-neutral-800',
                                )}
                            >
                                {value}
                            </button>
                        ))}
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            onClick={() => capture('manual')}
                            disabled={!cameraOn || busy}
                            title={
                                cameraOn
                                    ? undefined
                                    : 'The camera is not running'
                            }
                            className="inline-flex items-center gap-1.5 rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200"
                        >
                            {busy ? (
                                <LoaderCircle className="h-3.5 w-3.5 animate-spin" />
                            ) : pending ? (
                                <RotateCcw className="h-3.5 w-3.5" />
                            ) : (
                                <Camera className="h-3.5 w-3.5" />
                            )}
                            {pending ? 'Retake' : 'Snap'}
                        </button>

                        {!cameraOn && !cameraError && (
                            <span className="text-xs text-slate-500 dark:text-neutral-400">
                                Starting camera…
                            </span>
                        )}

                        {cameraOn && devices.length > 1 && (
                            <select
                                value={deviceId}
                                onChange={(e) => startCamera(e.target.value)}
                                aria-label="Camera"
                                className="max-w-48 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-200"
                            >
                                {devices.map((device, index) => (
                                    <option
                                        key={device.deviceId}
                                        value={device.deviceId}
                                    >
                                        {device.label || `Camera ${index + 1}`}
                                    </option>
                                ))}
                            </select>
                        )}

                        {cameraError && (
                            <button
                                type="button"
                                onClick={() =>
                                    startCamera(deviceId || undefined)
                                }
                                className="text-xs font-medium text-slate-600 underline dark:text-neutral-300"
                            >
                                Retry camera
                            </button>
                        )}
                    </div>

                    {(cameraError || uploadError) && (
                        <p className="text-xs text-amber-700 dark:text-amber-300">
                            {uploadError ?? cameraError}
                        </p>
                    )}
                    {!patient && !cameraError && (
                        <p className="text-xs text-slate-500 dark:text-neutral-400">
                            Snap now, or the photo is taken automatically once
                            the patient is selected or created.
                        </p>
                    )}
                </div>

                {pending && (
                    <div className="flex items-center gap-2">
                        <img
                            src={pending.url}
                            alt={`Slip photo (${pending.subject_label})`}
                            className="h-16 w-16 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-neutral-700"
                        />
                        <div className="text-xs">
                            <p className="font-semibold text-slate-800 dark:text-neutral-100">
                                {pending.subject_label} photo ready
                            </p>
                            <p className="text-slate-500 dark:text-neutral-400">
                                {pending.source === 'auto'
                                    ? 'Captured automatically'
                                    : 'Snapped'}{' '}
                                {patient
                                    ? ' · added to the next slip'
                                    : ' · goes to the patient you select next'}
                            </p>
                        </div>
                    </div>
                )}
            </div>
            {children}
        </SlipCameraContext.Provider>
    );
}
