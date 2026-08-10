import { apiClosingsSearch, apiDrugsSearch, apiIcd10Codes, apiUsersSearch } from '@/routes';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import {
    Bold,
    ClassicEditor,
    Essentials,
    Italic,
    List,
    Mention,
    Paragraph,
    Underline,
    type MentionFeedObjectItem,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';

// Mention feed markers:
//   #  drug            (reuses /api/drugs/search)
//   @  doctor          (reuses /api/users/search, doctor_only=true)
//   &  ICD-10 code      (reuses /api/icd10-codes)
//   !  closing/transaction number (reuses /api/closings/search)

function getCsrf() {
    return decodeURIComponent(
        document.cookie.split('XSRF-TOKEN=')[1]?.split(';')[0] ?? '',
    );
}

interface MentionItem extends MentionFeedObjectItem {
    label: string;
    detail?: string;
}

async function fetchJson(url: string, init?: RequestInit) {
    try {
        const res = await fetch(url, {
            ...init,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(init?.headers ?? {}),
            },
        });
        if (!res.ok) return null;
        return await res.json();
    } catch {
        return null;
    }
}

async function drugFeed(queryText: string): Promise<MentionItem[]> {
    const json = await fetchJson(
        `${apiDrugsSearch().url}?q=${encodeURIComponent(queryText)}&limit=10`,
    );
    return (json?.data ?? []).map(
        (drug: { name: string; generic_name?: string }) => ({
            id: `#${drug.name}`,
            label: drug.name,
            detail: drug.generic_name,
        }),
    );
}

async function doctorFeed(queryText: string): Promise<MentionItem[]> {
    const json = await fetchJson(apiUsersSearch().url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': getCsrf(),
        },
        body: JSON.stringify({ name: queryText, doctor_only: true, limit: 10 }),
    });
    const exact = json?.data?.exact ?? [];
    const possible = json?.data?.possible ?? [];
    const seen = new Set<number>();
    return [...exact, ...possible]
        .filter((user: { id: number }) => {
            if (seen.has(user.id)) return false;
            seen.add(user.id);
            return true;
        })
        .map((user: { name: string }) => ({ id: `@${user.name}`, label: user.name }));
}

async function icdFeed(queryText: string): Promise<MentionItem[]> {
    const json = await fetchJson(
        `${apiIcd10Codes().url}?q=${encodeURIComponent(queryText)}`,
    );
    return (json?.data ?? []).map(
        (icd: { code: string; description?: string }) => ({
            id: `&${icd.code}`,
            label: icd.code,
            detail: icd.description,
        }),
    );
}

async function closingFeed(queryText: string): Promise<MentionItem[]> {
    const json = await fetchJson(apiClosingsSearch().url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': getCsrf(),
        },
        body: JSON.stringify({ search: queryText, limit: 10 }),
    });
    return (json?.data ?? []).map((closing: { ct_number: string }) => ({
        id: `!${closing.ct_number}`,
        label: closing.ct_number,
    }));
}

function renderMentionItem(feedItem: MentionFeedObjectItem): HTMLElement {
    // itemRenderer is typed against the base MentionFeedObjectItem (id/text
    // only), but the feed functions above always populate MentionItem's
    // extra label/detail fields — safe to assume here.
    const item = feedItem as MentionItem;

    const wrapper = document.createElement('span');
    wrapper.style.display = 'flex';
    wrapper.style.flexDirection = 'column';
    wrapper.style.padding = '2px 4px';

    const label = document.createElement('span');
    label.style.fontWeight = '600';
    label.textContent = item.label;
    wrapper.appendChild(label);

    if (item.detail) {
        const detail = document.createElement('span');
        detail.style.fontSize = '11px';
        detail.style.color = '#64748b';
        detail.textContent = item.detail;
        wrapper.appendChild(detail);
    }

    return wrapper;
}

interface ReferralNotesEditorProps {
    value: string;
    onChange: (html: string) => void;
    disabled?: boolean;
}

export default function ReferralNotesEditor({
    value,
    onChange,
    disabled = false,
}: ReferralNotesEditorProps) {
    return (
        <div className="rounded-lg border border-slate-200 [&_.ck-editor__editable]:min-h-[140px] [&_.ck-editor__editable]:px-3 [&_.ck-editor__editable]:py-2">
            <CKEditor
                editor={ClassicEditor}
                disabled={disabled}
                data={value}
                onChange={(_event, editor) => onChange(editor.getData())}
                config={{
                    licenseKey: 'GPL',
                    plugins: [Essentials, Paragraph, Bold, Italic, Underline, List, Mention],
                    toolbar: [
                        'bold',
                        'italic',
                        'underline',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'undo',
                        'redo',
                    ],
                    mention: {
                        feeds: [
                            { marker: '#', feed: drugFeed, itemRenderer: renderMentionItem, minimumCharacters: 1 },
                            { marker: '@', feed: doctorFeed, itemRenderer: renderMentionItem, minimumCharacters: 1 },
                            { marker: '&', feed: icdFeed, itemRenderer: renderMentionItem, minimumCharacters: 1 },
                            { marker: '!', feed: closingFeed, itemRenderer: renderMentionItem, minimumCharacters: 1 },
                        ],
                    },
                }}
            />
        </div>
    );
}
