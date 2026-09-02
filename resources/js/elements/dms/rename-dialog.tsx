import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import dms from '@/routes/dms';
import { type DmsDocument, type DmsFolder } from '@/types';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

interface RenameDialogProps {
    target:
        | { type: 'folder'; item: DmsFolder }
        | { type: 'document'; item: DmsDocument };
    onClose: () => void;
}

export default function DmsRenameDialog({
    target,
    onClose,
}: RenameDialogProps) {
    const { data, setData, patch, processing, errors, reset } = useForm({
        name: target.item.name,
    });

    useEffect(() => {
        setData('name', target.item.name);
    }, [target.item.uuid]);

    function submit(event: React.FormEvent) {
        event.preventDefault();

        const url =
            target.type === 'folder'
                ? dms.folders.update(target.item).url
                : dms.documents.update(target.item).url;

        patch(url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Renamed.');
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
                        <DialogTitle>Rename {target.type}</DialogTitle>
                    </DialogHeader>
                    <div className="grid gap-2 py-4">
                        <Label htmlFor="rename-name">Name</Label>
                        <Input
                            id="rename-name"
                            value={data.name}
                            onChange={(event) =>
                                setData('name', event.target.value)
                            }
                            autoFocus
                        />
                        {errors.name && (
                            <p className="text-sm text-destructive">
                                {errors.name}
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
                            Save
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
