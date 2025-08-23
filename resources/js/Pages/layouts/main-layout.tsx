import React, { useEffect } from "react";
import Nabar from "./navbar";
import Footer from "./footer";
import { usePage } from "@inertiajs/react";
import { toast } from "sonner";

type Props = {
    children: React.ReactNode;
    alert?: React.ReactNode;
};

const MainLayout = ({ children, alert }: Props) => {
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
        <div className="w-full sm:max-w-md bg-foreground relative mx-auto min-h-screen flex flex-col">
            {/* Main content area */}
            <Footer />
            <main className="flex-1 mt-10 relative sm:max-w-md w-full overflow-y-auto mb-16">
                {alert && (
                    <div className="bg-red-100 w-full h-8 items-center  flex justify-center">
                        {alert}
                    </div>
                )}
                {children}
            </main>
            {/* Navbar and Footer fixed at the bottom, Footer above Navbar */}
            <div className="fixed bottom-0 left-1/2 -translate-x-1/2 w-full  sm:max-w-md z-50 flex flex-col">
                <Nabar />
            </div>
        </div>
    );
};

export default MainLayout;
