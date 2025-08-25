import "./bootstrap";
import '../css/app.css';
import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";
import { Toaster } from "./components/ui/sonner";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.tsx", { eager: true });
        const page = pages[`./Pages/${name}.tsx`];

         if (!page) {
            throw new Error(`Page not found: ${name}`);
        }

        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(
            <>
                <Toaster />
                <App {...props} />
            </>
        );
    },
    progress: {
        showSpinner: true
    },
    
});
