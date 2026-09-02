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
import { type DmsDocument } from '@/types';
import { useForm } from '@inertiajs/react';
import { toast } from 'sonner';

interface ShareDialogProps {
    document: DmsDocument;
    onClose: () => void;
}

export default function DmsShareDialog({
    document,
    onClose,
}: ShareDialogProps) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        ability: 'view',
    });

    function submit(event: React.FormEvent) {
        event.preventDefault();

        post(dms.documents.share(document).url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Document shared.');
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
                        <DialogTitle>Share "{document.name}"</DialogTitle>
                    </DialogHeader>
                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="share-email">Email</Label>
                            <Input
                                id="share-email"
                                type="email"
                                value={data.email}
                                onChange={(event) =>
                                    setData('email', event.target.value)
                                }
                                autoFocus
                            />
                            {errors.email && (
                                <p className="text-sm text-destructive">
                                    {errors.email}
                                </p>
                            )}
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="share-ability">Access</Label>
                            <Select
                                value={data.ability}
                                onValueChange={(value) =>
                                    setData('ability', value)
                                }
                            >
                                <SelectTrigger id="share-ability">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="view">
                                        Can view
                                    </SelectItem>
                                    <SelectItem value="edit">
                                        Can edit
                                    </SelectItem>
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
                        <Button type="submit" disabled={processing}>
                            Send
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
