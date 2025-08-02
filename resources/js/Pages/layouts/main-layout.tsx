import React from "react";
import Nabar from "./navbar";
import Footer from "./footer";

const MainLayout = ({ children }) => {
    return (
        <div className="bg-[var(--clr-surface-a0)] h-screen">
            <div className="w-full max-w-md bg-[var(--clr-surface-a0)] relative border-[var(--clr-surface-a20)] mx-auto h-screen">
                <Footer />
                <main className="flex-1 p-3 overflow-y-auto overflow-hidden">
                    {children}
                </main>
                <Nabar />
            </div>
        </div>
    );
};

export default MainLayout;
