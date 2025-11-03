import React from 'react';

interface PageTemplateProps {
    title: string;
    subtitle?: string;
    children: React.ReactNode;
}

const PageTemplate: React.FC<PageTemplateProps> = ({ title, subtitle, children }) => {
    return (
        <div className="py-8">
            <div className="px-4 sm:px-6 lg:px-8">
            <header className="mb-8">
                <h1 className="text-3xl font-bold text-gray-900 dark:text-gray-100">{title}</h1>
                {subtitle && <h2 className="mt-2 text-lg text-gray-600 dark:text-gray-400">{subtitle}</h2>}
            </header>
            <main className="">
                {children}
            </main>
            </div>
        </div>
    );
};

export default PageTemplate;