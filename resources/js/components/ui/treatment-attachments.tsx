import { clsx } from 'clsx';
import { FileText, Trash2, UploadCloud } from 'lucide-react';
import { useRef, useState } from 'react';
import { toast } from 'sonner';

export interface TreatmentAttachmentData {
    id: number;
    file_name: string;
    file_type: string;
    label?: string | null;
    url: string;
}

interface TreatmentAttachmentsProps {
    attachments: TreatmentAttachmentData[];
    uploadUrl: string;
    deleteUrlFor: (attachmentId: number) => string;
    onUploaded: (attachment: TreatmentAttachmentData) => void;
    onDeleted: (attachmentId: number) => void;
    disabled?: boolean;
    className?: string;
}

function getCsrf() {
    return decodeURIComponent(document.cookie.split('XSRF-TOKEN=')[1]?.split(';')[0] ?? '');
}

export default function TreatmentAttachments({
    attachments,
    uploadUrl,
    deleteUrlFor,
    onUploaded,
    onDeleted,
    disabled = false,
    className,
}: TreatmentAttachmentsProps) {
    const [uploading, setUploading] = useState(false);
    const [deletingId, setDeletingId] = useState<number | null>(null);
    const inputRef = useRef<HTMLInputElement>(null);

    const handleFiles = async (files: FileList | null) => {
        if (!files || files.length === 0) return;
        const file = files[0];

        setUploading(true);
        try {
            const body = new FormData();
            body.append('file', file);

            const res = await fetch(uploadUrl, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': getCsrf() },
                body,
            });
            const json = await res.json();
            if (!res.ok) {
                toast.error(json.message ?? 'Upload failed');
                return;
            }
            onUploaded(json.data);
            toast.success('Attachment uploaded');
        } catch {
            toast.error('Network error — please try again');
        } finally {
            setUploading(false);
            if (inputRef.current) inputRef.current.value = '';
        }
    };

    const handleDelete = async (attachmentId: number) => {
        setDeletingId(attachmentId);
        try {
            const res = await fetch(deleteUrlFor(attachmentId), {
                method: 'DELETE',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': getCsrf() },
            });
            if (!res.ok) {
                toast.error('Failed to delete attachment');
                return;
            }
            onDeleted(attachmentId);
        } catch {
            toast.error('Network error — please try again');
        } finally {
            setDeletingId(null);
        }
    };

    return (
        <div className={clsx('space-y-3', className)}>
            {!disabled && (
                <label
                    className={clsx(
                        'flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/60 py-6 text-center transition-colors hover:border-teal-400 hover:bg-teal-50/40',
                        uploading && 'pointer-events-none opacity-60',
                    )}
                >
                    <UploadCloud className="h-6 w-6 text-slate-400" />
                    <span className="text-xs font-medium text-slate-600">
                        {uploading ? 'Uploading…' : 'Click to upload image or PDF'}
                    </span>
                    <span className="text-[10px] text-slate-400">JPG, PNG, or PDF — max 10MB</span>
                    <input
                        ref={inputRef}
                        type="file"
                        accept="image/jpeg,image/png,application/pdf"
                        className="hidden"
                        disabled={uploading}
                        onChange={(e) => handleFiles(e.target.files)}
                    />
                </label>
            )}

            {attachments.length > 0 && (
                <ul className="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
                    {attachments.map((attachment) => {
                        const isImage = attachment.file_type.startsWith('image/');
                        return (
                            <li key={attachment.id} className="group relative overflow-hidden rounded-lg border border-slate-200 bg-white">
                                <a href={attachment.url} target="_blank" rel="noopener noreferrer" className="block">
                                    {isImage ? (
                                        <img src={attachment.url} alt={attachment.file_name} className="h-24 w-full object-cover" />
                                    ) : (
                                        <div className="flex h-24 w-full flex-col items-center justify-center gap-1 bg-slate-50">
                                            <FileText className="h-6 w-6 text-slate-400" />
                                            <span className="px-2 text-center text-[10px] text-slate-500">PDF</span>
                                        </div>
                                    )}
                                </a>
                                <p className="truncate px-1.5 py-1 text-[10px] text-slate-500">{attachment.label || attachment.file_name}</p>
                                {!disabled && (
                                    <button
                                        type="button"
                                        disabled={deletingId === attachment.id}
                                        onClick={() => handleDelete(attachment.id)}
                                        className="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-white/90 text-slate-500 opacity-0 shadow transition-opacity group-hover:opacity-100 hover:text-red-600 disabled:opacity-50"
                                    >
                                        <Trash2 className="h-3.5 w-3.5" />
                                    </button>
                                )}
                            </li>
                        );
                    })}
                </ul>
            )}
        </div>
    );
}
