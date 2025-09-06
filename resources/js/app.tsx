import "../css/app.css";

import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.jsx", { eager: true });
        return pages[`./Pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});

// import { createInertiaApp } from '@inertiajs/react';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { createRoot } from 'react-dom/client';
// import { Toaster } from './components/ui/sonner';

// const appName = import.meta.env.VITE_APP_NAME || 'Starpick';

// createInertiaApp({
//     title: (title) => (title ? `${title} - ${appName}` : appName),
//     resolve: (name) => resolvePageComponent(`./Pages/${name}.tsx`, import.meta.glob('./Pages/**/*.tsx')),
//     setup({ el, App, props }) {
//         const root = createRoot(el);

//         root.render(
//             <>
//                 <Toaster />
//                 <App {...props} />
//             </>,
//         );
//     },
//     progress: {
//         color: '#4B5563',
//         showSpinner: true
//     },
// });
