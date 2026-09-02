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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import dms from '@/routes/dms';
import { type DmsClassification } from '@/types';
import { useForm } from '@inertiajs/react';
import { toast } from 'sonner';

interface CreateFolderDialogProps {
    parentUuid: string | null;
    classifications: DmsClassification[];
    onClose: () => void;
}

export default function DmsCreateFolderDialog({
    parentUuid,
    classifications,
    onClose,
}: CreateFolderDialogProps) {
    const { data, setData, transform, post, processing, errors, reset } =
        useForm({
            name: '',
            classification_id: '',
        });

    function submit(event: React.FormEvent) {
        event.preventDefault();

        transform((formData) => ({
            name: formData.name,
            classification_id: formData.classification_id || null,
            parent_uuid: parentUuid,
        }));

        post(dms.folders.store().url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Folder created.');
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
                        <DialogTitle>New folder</DialogTitle>
                    </DialogHeader>
                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="folder-name">Name</Label>
                            <Input
                                id="folder-name"
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
                        <div className="grid gap-2">
                            <Label htmlFor="folder-classification">
                                Classification (optional)
                            </Label>
                            <Select
                                value={data.classification_id}
                                onValueChange={(value) =>
                                    setData('classification_id', value)
                                }
                            >
                                <SelectTrigger id="folder-classification">
                                    <SelectValue placeholder="None" />
                                </SelectTrigger>
                                <SelectContent>
                                    {classifications.map((classification) => (
                                        <SelectItem
                                            key={classification.id}
                                            value={String(classification.id)}
                                        >
                                            {classification.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={onClose}
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            disabled={processing || !data.name}
                        >
                            Create
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
