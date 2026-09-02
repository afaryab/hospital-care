const OFFICE_EDITABLE_EXTENSIONS = [
    'doc',
    'docx',
    'odt',
    'rtf',
    'xls',
    'xlsx',
    'ods',
    'csv',
    'ppt',
    'pptx',
    'odp',
    'txt',
];

export function extensionOf(name: string): string {
    const parts = name.split('.');

    return parts.length > 1 ? parts[parts.length - 1].toLowerCase() : '';
}

export function isOfficeEditable(name: string): boolean {
    return OFFICE_EDITABLE_EXTENSIONS.includes(extensionOf(name));
}
