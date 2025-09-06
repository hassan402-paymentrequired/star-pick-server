import "./bootstrap.js";
import "../css/app.css";

import { Toaster } from "./components/ui/sonner";
import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";

const appName = import.meta.env.VITE_APP_NAME || "Starpick";

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.tsx", { eager: true });
        return pages[`./Pages/${name}.tsx`];
    },
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <>
                <Toaster />
                <App {...props} />
            </>
        );
    },
    progress: {
        color: "#4B5563",
        showSpinner: true,
    },
});
