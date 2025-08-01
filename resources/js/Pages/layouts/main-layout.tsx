import React from "react";
import Nabar from "./navbar";
import Footer from "./footer";

const MainLayout = ({ children }) => {
    return (
        <div className="bg-gray-50">
            <div className="w-full max-w-md  bg-white relative border mx-auto  h-screen">
                <Footer />
                <main className="flex-1 p-3">{children}</main>
                <Nabar />
            </div>
        </div>
    );
};

export default MainLayout;
