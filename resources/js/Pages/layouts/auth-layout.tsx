import { usePage } from "@inertiajs/react";
import React, { useEffect } from "react";
import { toast } from "sonner";

const AuthLayout = ({ children }) => {
    const {
        flash: { success, error },
    } = usePage<{ flash: { success: string; error: string } }>().props;

    useEffect(() => {
        if (success && !success.startsWith("https")) {
            toast.success(success);
        }

        if (error) {
            toast.error(error);
        }
    }, [success, error]);

    return (
        <div className="">
            <div className="w-full bg-foreground h-screen flex overflow-hidden overflow-y-auto flex-col max-w-md   relative mx-auto ">
                <main className="flex-1 p-2 mx-auto w-full">{children}</main>
                <div className="h-13 flex w-full py-2 px-4 justify-center items-center">
                    <p className="text-sm">
                        All right reserved. Starpick &copy; 2025
                    </p>
                </div>
            </div>
        </div>
    );
};

export default AuthLayout;
