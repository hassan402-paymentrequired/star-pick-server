import React from "react";

const AuthLayout = ({ children }) => {
    return (
        <div className="">
            <div className="w-full bg-foreground h-screen flex overflow-hidden overflow-y-auto flex-col max-w-md   relative mx-auto ">
                <main className="flex-1 p-5 mx-auto">{children}</main>
                <div className="h-13 flex w-full py-2 px-4 justify-center items-center">
                    <p className="text-sm">All right reserved. Starpick &copy; 2025</p>
                </div>
            </div>
        </div>
    );
};

export default AuthLayout;
