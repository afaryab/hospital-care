import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { patientPhotoStore } from '@/routes';
import { router } from '@inertiajs/react';
import { clsx } from 'clsx';
import { Camera, ImageUp, RotateCcw, Video } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';

type PatientPhotoDialogProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    psNumber: string;
    currentPhotoUrl?: string | null;
};

type Mode = 'camera' | 'upload';

const cameraSupported = () =>
    typeof window !== 'undefined' &&
    window.isSecureContext &&
    !!navigator.mediaDevices?.getUserMedia;

export default function PatientPhotoDialog({
    open,
    onOpenChange,
    psNumber,
    currentPhotoUrl,
}: PatientPhotoDialogProps) {
    const videoRef = useRef<HTMLVideoElement>(null);
    const streamRef = useRef<MediaStream | null>(null);

    const [mode, setMode] = useState<Mode>('camera');
    const [cameraOn, setCameraOn] = useState(false);
    const [cameraError, setCameraError] = useState<string | null>(null);
    const [devices, setDevices] = useState<MediaDeviceInfo[]>([]);
    const [deviceId, setDeviceId] = useState<string>('');
    const [photo, setPhoto] = useState<Blob | null>(null);
    const [preview, setPreview] = useState<string | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [saving, setSaving] = useState(false);

    const stopCamera = useCallback(() => {
        streamRef.current?.getTracks().forEach((track) => track.stop());
        streamRef.current = null;
        setCameraOn(false);
    }, []);

    const clearPhoto = useCallback(() => {
        setPhoto(null);
        setPreview((url) => {
            if (url) {
                URL.revokeObjectURL(url);
            }
            return null;
        });
    }, []);

    const startCamera = async (selectedDeviceId?: string) => {
        setCameraError(null);
        clearPhoto();
        stopCamera();

        if (!cameraSupported()) {
            setCameraError(
                'The browser only allows camera access over HTTPS or on localhost. Open the app over HTTPS to use the webcam, or upload a photo instead.',
            );
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: selectedDeviceId
                    ? { deviceId: { exact: selectedDeviceId } }
                    : { facingMode: 'user' },
                audio: false,
            });
            streamRef.current = stream;
            setCameraOn(true);

            if (videoRef.current) {
                videoRef.current.srcObject = stream;
                await videoRef.current.play();
            }

            const allDevices = await navigator.mediaDevices.enumerateDevices();
            const cameras = allDevices.filter((d) => d.kind === 'videoinput');
            setDevices(cameras);
            setDeviceId(
                selectedDeviceId ??
                    stream.getVideoTracks()[0]?.getSettings().deviceId ??
                    '',
            );
        } catch (e) {
            const name = e instanceof DOMException ? e.name : '';
            setCameraError(
                name === 'NotAllowedError'
                    ? 'Camera permission was denied. Allow camera access in the browser and try again.'
                    : name === 'NotFoundError'
                      ? 'No webcam was found. Check that the camera is attached.'
                      : 'The camera could not be started. It may be in use by another application.',
            );
            stopCamera();
        }
    };

    const snap = () => {
        const video = videoRef.current;
        if (!video || !video.videoWidth) {
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d')?.drawImage(video, 0, 0);

        canvas.toBlob(
            (blob) => {
                if (!blob) {
                    return;
                }
                setPhoto(blob);
                setPreview(URL.createObjectURL(blob));
                stopCamera();
            },
            'image/jpeg',
            0.9,
        );
    };

    const chooseFile = (file: File | undefined) => {
        clearPhoto();
        if (!file) {
            return;
        }
        setPhoto(file);
        setPreview(URL.createObjectURL(file));
    };

    const save = () => {
        if (!photo) {
            return;
        }

        const [, year, month, number] = psNumber.split('/');
        const file =
            photo instanceof File
                ? photo
                : new File([photo], 'patient-photo.jpg', {
                      type: 'image/jpeg',
                  });

        setSaving(true);
        setError(null);
        router.post(
            patientPhotoStore({ year, month, number }).url,
            { photo: file, source: mode },
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => handleOpenChange(false),
                onError: (errors) =>
                    setError(
                        errors.photo ??
                            errors.source ??
                            'The photo could not be saved.',
                    ),
                onFinish: () => setSaving(false),
            },
        );
    };

    const handleOpenChange = (next: boolean) => {
        if (!next) {
            stopCamera();
            clearPhoto();
            setCameraError(null);
            setError(null);
        }
        onOpenChange(next);
    };

    useEffect(() => stopCamera, [stopCamera]);

    const switchMode = (next: Mode) => {
        stopCamera();
        clearPhoto();
        setError(null);
        setMode(next);
    };

    return (
        <Dialog open={open} onOpenChange={handleOpenChange}>
            <DialogContent className="sm:max-w-xl">
                <DialogHeader>
                    <DialogTitle>Patient photo</DialogTitle>
                    <DialogDescription>
                        {psNumber} — capture a photo with the attached webcam,
                        or upload one.
                    </DialogDescription>
                </DialogHeader>

                <div className="flex gap-2">
                    <Button
                        type="button"
                        variant={mode === 'camera' ? 'default' : 'outline'}
                        onClick={() => switchMode('camera')}
                    >
                        <Camera /> Webcam
                    </Button>
                    <Button
                        type="button"
                        variant={mode === 'upload' ? 'default' : 'outline'}
                        onClick={() => switchMode('upload')}
                    >
                        <ImageUp /> Upload
                    </Button>
                </div>

                <div className="relative flex aspect-[4/3] w-full items-center justify-center overflow-hidden rounded-lg bg-neutral-900">
                    <video
                        ref={videoRef}
                        muted
                        playsInline
                        className={clsx(
                            'h-full w-full -scale-x-100 object-cover',
                            !(mode === 'camera' && cameraOn && !preview) &&
                                'hidden',
                        )}
                    />
                    {preview && (
                        <img
                            src={preview}
                            alt="New patient photo"
                            className="h-full w-full object-contain"
                        />
                    )}
                    {!preview && !(mode === 'camera' && cameraOn) && (
                        <div className="flex flex-col items-center gap-3 p-6 text-center text-sm text-neutral-300">
                            {currentPhotoUrl ? (
                                <img
                                    src={currentPhotoUrl}
                                    alt="Current patient photo"
                                    className="h-32 w-32 rounded-lg object-cover"
                                />
                            ) : null}
                            {mode === 'camera'
                                ? 'Press “Start camera” to open the live view.'
                                : 'Choose an image file to preview it here.'}
                        </div>
                    )}
                </div>

                {mode === 'camera' && (
                    <div className="flex flex-wrap items-center gap-2">
                        {!cameraOn && !preview && (
                            <Button
                                type="button"
                                onClick={() =>
                                    startCamera(deviceId || undefined)
                                }
                            >
                                <Video /> Start camera
                            </Button>
                        )}
                        {cameraOn && (
                            <Button type="button" onClick={snap}>
                                <Camera /> Snap
                            </Button>
                        )}
                        {preview && (
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() =>
                                    startCamera(deviceId || undefined)
                                }
                            >
                                <RotateCcw /> Retake
                            </Button>
                        )}
                        {cameraOn && devices.length > 1 && (
                            <Select
                                value={deviceId}
                                onValueChange={(value) => startCamera(value)}
                            >
                                <SelectTrigger className="w-56">
                                    <SelectValue placeholder="Camera" />
                                </SelectTrigger>
                                <SelectContent>
                                    {devices.map((device, index) => (
                                        <SelectItem
                                            key={device.deviceId}
                                            value={device.deviceId}
                                        >
                                            {device.label ||
                                                `Camera ${index + 1}`}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        )}
                    </div>
                )}

                {mode === 'upload' && (
                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        onChange={(e) => chooseFile(e.target.files?.[0])}
                        className="text-sm"
                    />
                )}

                <InputError message={cameraError ?? error ?? undefined} />

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => handleOpenChange(false)}
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        onClick={save}
                        disabled={!photo || saving}
                    >
                        {saving ? 'Saving…' : 'Save photo'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
