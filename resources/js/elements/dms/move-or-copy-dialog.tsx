import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import dms from '@/routes/dms';
import {
    type DmsDocument,
    type DmsFolder,
    type DmsFolderOption,
} from '@/types';
import { useForm } from '@inertiajs/react';
import { toast } from 'sonner';

interface MoveOrCopyDialogProps {
    mode: 'move' | 'copy';
    target:
        | { type: 'folder'; item: DmsFolder }
        | { type: 'document'; item: DmsDocument };
    folderOptions: DmsFolderOption[];
    onClose: () => void;
}

export default function DmsMoveOrCopyDialog({
    mode,
    target,
    folderOptions,
    onClose,
}: MoveOrCopyDialogProps) {
    const { data, setData, post, processing, errors, reset } = useForm({
        target_uuid: '',
    });

    function submit(event: React.FormEvent) {
        event.preventDefault();

        if (!data.target_uuid) {
            return;
        }

        const routes = target.type === 'folder' ? dms.folders : dms.documents;
        const action = mode === 'move' ? routes.move : routes.copy;

        post(action(target.item).url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(mode === 'move' ? 'Moved.' : 'Copied.');
                reset();
                onClose();
            },
        });
    }

    const selectableFolders = folderOptions.filter(
        (option) =>
            target.type !== 'folder' || option.uuid !== target.item.uuid,
    );

    return (
        <Dialog open onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>
                            {mode === 'move' ? 'Move' : 'Copy'} "
                            {target.item.name}"
                        </DialogTitle>
                    </DialogHeader>
                    <div className="grid gap-2 py-4">
                        <Label htmlFor="target-folder">
                            Destination folder
                        </Label>
                        <Select
                            value={data.target_uuid}
                            onValueChange={(value) =>
                                setData('target_uuid', value)
                            }
                        >
                            <SelectTrigger id="target-folder">
                                <SelectValue placeholder="Select a folder" />
                            </SelectTrigger>
                            <SelectContent>
                                {selectableFolders.map((option) => (
                                    <SelectItem
                                        key={option.uuid}
                                        value={option.uuid}
                                    >
                                        {option.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.target_uuid && (
                            <p className="text-sm text-destructive">
                                {errors.target_uuid}
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
                        <Button
                            type="submit"
                            disabled={processing || !data.target_uuid}
                        >
                            {mode === 'move' ? 'Move' : 'Copy'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
