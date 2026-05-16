interface TablePaginationProps {
    currentPage: number;
    lastPage: number;
    makeHref: (page: number) => string;
}

function buildPageRange(curr: number, last: number): (number | '...')[] {
    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => i + 1);
    }
    const delta = 1;
    const range: number[] = [];
    for (let i = Math.max(2, curr - delta); i <= Math.min(last - 1, curr + delta); i++) {
        range.push(i);
    }
    const pages: (number | '...')[] = [1];
    if (range.length && range[0] > 2) pages.push('...');
    pages.push(...range);
    if (range.length && range[range.length - 1] < last - 1) pages.push('...');
    pages.push(last);
    return pages;
}

export default function TablePagination({ currentPage, lastPage, makeHref }: TablePaginationProps) {
    if (lastPage <= 1) return null;

    const pages = buildPageRange(currentPage, lastPage);

    return (
        <nav className="flex items-center justify-center gap-2 py-2" aria-label="Pagination">
            {currentPage > 1 ? (
                <a
                    href={makeHref(currentPage - 1)}
                    className="rounded border bg-white px-3 py-1 text-[#1c398e] hover:underline"
                    aria-label="Previous page"
                >
                    ‹
                </a>
            ) : (
                <span className="rounded border bg-gray-100 px-3 py-1 text-gray-400" aria-hidden="true">‹</span>
            )}

            {pages.map((p, idx) =>
                p === '...' ? (
                    <span key={`dots-${idx}`} className="px-3 py-1 text-gray-500">…</span>
                ) : p === currentPage ? (
                    <span
                        key={p}
                        aria-current="page"
                        className="rounded bg-[#06df72] px-3 py-1 font-medium text-white dark:bg-neutral-800"
                    >
                        {p}
                    </span>
                ) : (
                    <a
                        key={p}
                        href={makeHref(p)}
                        className="rounded border bg-white px-3 py-1 text-[#1c398e] hover:underline"
                    >
                        {p}
                    </a>
                ),
            )}

            {currentPage < lastPage ? (
                <a
                    href={makeHref(currentPage + 1)}
                    className="rounded border bg-white px-3 py-1 text-[#1c398e] hover:underline"
                    aria-label="Next page"
                >
                    ›
                </a>
            ) : (
                <span className="rounded border bg-gray-100 px-3 py-1 text-gray-400" aria-hidden="true">›</span>
            )}
        </nav>
    );
}
