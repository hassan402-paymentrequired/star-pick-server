import React from "react";

import {
    InputOTP,
    InputOTPGroup,
    InputOTPSeparator,
    InputOTPSlot,
} from "@/components/ui/input-otp";
import { GalleryVerticalEnd, LoaderIcon } from "lucide-react";
import { Link, useForm, usePage } from "@inertiajs/react";
import { Button } from "../button";
import FormError from "@/components/error";
import { toast } from "sonner";
import { Label } from "../label";
import { Input } from "../input";

export function ForgotPassword() {
    const { errors: globalErrors, flash } = usePage().props;
    const { data, setData, processing, post } = useForm({
        phone: "",
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("forgot.password.check"), {
            onSuccess: () => {
                if (flash?.success) {
                    toast.success(flash?.success);
                }
            },
            onError: () => {
                toast.error("Opps!. Invalid otp");
            },
            onFinish: () => {
                if (flash?.error || globalErrors.otp) {
                    toast.error(flash?.error || globalErrors.otp);
                }
                if (flash?.success) {
                    toast.error(flash?.success);
                }
            },
            preserveScroll: true,
        });
    };

    // console.log(globalErrors, flash);

    return (
        <div className="flex flex-col gap-6 items-center justify-center">
            <div className="flex flex-col items-center gap-2">
                <a
                    href="#"
                    className="flex flex-col items-center gap-2 font-medium"
                >
                    <div className="flex size-8 items-center justify-center rounded-md">
                        <GalleryVerticalEnd className="size-6" />
                    </div>
                    <span className="sr-only">Acme Inc.</span>
                </a>
                <h1 className="text-xl font-bold">
                    Enter your registered phone number
                </h1>
                <div className="text-center text-sm">
                    or{" "}
                    <Link
                        href={route("register")}
                        className="underline underline-offset-4"
                        prefetch
                    >
                        Go back
                    </Link>
                </div>
            </div>
            <form
                onSubmit={submit}
                className="flex flex-col gap-3 w-full "
            >
                <div className="grid gap-1">
                    <Label htmlFor="phone">Phone</Label>
                    <Input
                        id="phone"
                        type="text"
                        placeholder="+234 000 000 0000"
                        required
                        value={data.phone}
                        onChange={(e) => setData("phone", e.target.value)}
                    />
                    {globalErrors.phone && (
                        <FormError message={globalErrors.phone} />
                    )}
                </div>
               <Button type="submit" className="w-full" disabled={processing}>
                    {processing && <LoaderIcon className="animate-spin" />}{" "}
                    Send
                </Button>
            </form>
        </div>
    );
}
