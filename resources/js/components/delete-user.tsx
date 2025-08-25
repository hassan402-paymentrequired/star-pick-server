import { useForm } from "@inertiajs/react";
import { FormEventHandler, useRef } from "react";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import FormError from "./error";
import { Loader, LogOut } from "lucide-react";

export default function DeleteUser() {
    const passwordInput = useRef<HTMLInputElement>(null);
    const { data, setData, post, processing, reset, errors, clearErrors } =
        useForm<Required<{ password: string }>>({ password: "" });

    const handleLogout = () => {
        post(route("auth.logout"));
    };

    return (
        <div>
            <div className="space-y-4 rounded-lg border mt-auto border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
                <div className="relative space-y-0.5 text-red-600 dark:text-red-100">
                    <p className="font-medium">Warning</p>
                    <p className="text-sm">
                        Please proceed with caution, this cannot be undone.
                    </p>
                </div>

                <Button variant="destructive" onClick={handleLogout} disabled={processing}>
                    {" "}
                   {processing ? <Loader className="animate-spin" /> : <LogOut /> }Logout
                </Button>
            </div>
        </div>
    );
}
