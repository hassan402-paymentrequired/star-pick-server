import React from "react";

const AuthLayout = ({ children }) => {
    return (
        <div className="bg-gray-50">
            <div className="w-full h-screen flex flex-col max-w-md  bg-white relative border mx-auto ">
                <main className="flex-1 p-5">{children}</main>
                <div className="h-13 flex w-full py-2 px-4 justify-center items-center">
                    <p className="text-sm">All right reserved. Starpick &copy; 2025</p>
                </div>
            </div>
        </div>
    );
};

export default AuthLayout;
