import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import dms from '@/routes/dms';
import { useForm } from '@inertiajs/react';
import { toast } from 'sonner';

interface ZipUploadDialogProps {
    folderUuid: string;
    onClose: () => void;
}

export default function DmsZipUploadDialog({
    folderUuid,
    onClose,
}: ZipUploadDialogProps) {
    const { setData, transform, post, processing, errors, reset, progress } =
        useForm<{
            file: File | null;
        }>({
            file: null,
        });

    function submit(event: React.FormEvent) {
        event.preventDefault();

        transform((data) => ({
            file: data.file,
            folder_uuid: folderUuid,
        }));

        post(dms.zipUploads.store().url, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Zip extracted.');
                reset();
                onClose();
            },
        });
    }

    return (
        <Dialog open onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>Upload zip</DialogTitle>
                        <DialogDescription>
                            Every file inside the zip is extracted into this
                            folder as its own document.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="grid gap-2 py-4">
                        <Label htmlFor="zip-file">Zip file</Label>
                        <Input
                            id="zip-file"
                            type="file"
                            accept=".zip"
                            onChange={(event) =>
                                setData('file', event.target.files?.[0] ?? null)
                            }
                        />
                        {errors.file && (
                            <p className="text-sm text-destructive">
                                {errors.file}
                            </p>
                        )}
                        {progress && (
                            <p className="text-xs text-muted-foreground">
                                Uploading… {progress.percentage}%
                            </p>
                        )}
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={onClose}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" disabled={processing}>
                            Extract
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
