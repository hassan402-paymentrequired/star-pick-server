import React from "react";
import Nabar from "./navbar";
import Footer from "./footer";

const MainLayout = ({ children }) => {
    return (
        <div className="w-full max-w-md bg-foreground relative mx-auto min-h-screen flex flex-col">
            {/* Main content area */}
                <Footer />
            <main className="flex-1 mt-10 p-3 overflow-y-auto mb-16">{children}</main>
            {/* Navbar and Footer fixed at the bottom, Footer above Navbar */}
            <div className="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md z-50 flex flex-col">
                <Nabar />
            </div>
        </div>
    );
};

export default MainLayout;
