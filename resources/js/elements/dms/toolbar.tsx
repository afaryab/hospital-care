import { Button } from '@/components/ui/button';
import { FileArchive, FolderPlus, Upload } from 'lucide-react';
import { useRef } from 'react';

interface ToolbarProps {
    canUpload: boolean;
    uploadProgress: number | null;
    onCreateFolder: () => void;
    onUploadFiles: (files: FileList) => void;
    onUploadZip: () => void;
}

export default function DmsToolbar({
    canUpload,
    uploadProgress,
    onCreateFolder,
    onUploadFiles,
    onUploadZip,
}: ToolbarProps) {
    const inputRef = useRef<HTMLInputElement>(null);

    return (
        <div className="flex flex-wrap items-center gap-2">
            <Button type="button" size="sm" onClick={onCreateFolder}>
                <FolderPlus className="size-4" />
                New folder
            </Button>

            <input
                ref={inputRef}
                type="file"
                multiple
                className="hidden"
                onChange={(event) => {
                    if (event.target.files?.length) {
                        onUploadFiles(event.target.files);
                    }
                    event.target.value = '';
                }}
            />
            <Button
                type="button"
                variant="outline"
                size="sm"
                disabled={!canUpload}
                onClick={() => inputRef.current?.click()}
            >
                <Upload className="size-4" />
                Upload files
            </Button>

            <Button
                type="button"
                variant="outline"
                size="sm"
                disabled={!canUpload}
                onClick={onUploadZip}
            >
                <FileArchive className="size-4" />
                Upload zip
            </Button>

            {uploadProgress !== null && (
                <span className="text-xs text-muted-foreground">
                    Uploading… {uploadProgress}%
                </span>
            )}
        </div>
    );
}
