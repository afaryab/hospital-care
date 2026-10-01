import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { type SlipPhotoSummary } from '@/elements/counter/slip-camera';
import { clsx } from 'clsx';
import { CameraOff } from 'lucide-react';
import { useState } from 'react';

const formatCapturedAt = (value: string | null) =>
    value
        ? new Date(value).toLocaleString(undefined, {
              dateStyle: 'medium',
              timeStyle: 'short',
          })
        : '';

export default function SlipPhotoThumb({
    photo,
    trNumber,
    size = 'sm',
}: {
    photo?: SlipPhotoSummary | null;
    trNumber?: string;
    size?: 'sm' | 'lg';
}) {
    const [open, setOpen] = useState(false);

    if (!photo) {
        return (
            <span
                title="No photo was captured for this slip"
                className="inline-flex items-center gap-1 rounded border border-amber-300 bg-amber-50 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300"
            >
                <CameraOff className="h-3 w-3" /> NO PHOTO
            </span>
        );
    }

    const caption = `${photo.subject_label}${photo.captured_by ? ` · ${photo.captured_by}` : ''}${photo.captured_at ? ` · ${formatCapturedAt(photo.captured_at)}` : ''}`;

    return (
        <>
            <button
                type="button"
                onClick={() => setOpen(true)}
                title={`${caption} — click to enlarge`}
                className="group inline-flex items-center gap-2 text-left"
            >
                <img
                    src={photo.url}
                    alt={`Slip photo (${photo.subject_label})`}
                    loading="lazy"
                    className={clsx(
                        'rounded-md object-cover ring-1 ring-slate-200 transition group-hover:ring-2 group-hover:ring-slate-400 dark:ring-neutral-700 dark:group-hover:ring-neutral-500',
                        size === 'sm' ? 'h-9 w-9' : 'h-24 w-24',
                    )}
                />
                <span
                    className={clsx(
                        'rounded px-1.5 py-0.5 text-[10px] font-semibold',
                        photo.subject === 'guardian'
                            ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300'
                            : 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-200',
                    )}
                >
                    {photo.subject_label.toUpperCase()}
                </span>
            </button>

            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="sm:max-w-xl">
                    <DialogHeader>
                        <DialogTitle>
                            Slip photo{trNumber ? ` — ${trNumber}` : ''}
                        </DialogTitle>
                        <DialogDescription>
                            {caption}
                            {photo.source === 'auto'
                                ? ' · captured automatically'
                                : ' · snapped by receptionist'}
                        </DialogDescription>
                    </DialogHeader>
                    <img
                        src={photo.url}
                        alt={`Slip photo (${photo.subject_label})`}
                        className="w-full rounded-lg object-contain"
                    />
                </DialogContent>
            </Dialog>
        </>
    );
}
