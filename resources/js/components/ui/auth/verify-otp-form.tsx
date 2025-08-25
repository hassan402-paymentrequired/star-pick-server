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

export function OtpVerification() {
    const { errors: globalErrors, flash } = usePage().props;
    const { data, setData, processing, post } = useForm({
        otp: "",
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("verify.store"), {
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
                    Verify Otp sent to your phone no.
                </h1>
                <div className="text-center text-sm">
                    Don&apos;t have an account?{" "}
                    <Link
                        href={route("register")}
                        className="underline underline-offset-4"
                        prefetch
                    >
                        Sign up
                    </Link>
                </div>
            </div>
            <form
                onSubmit={submit}
                className="flex flex-col gap-3 w-full items-center justify-center"
            >
                <InputOTP
                    maxLength={6}
                    value={data.otp}
                    onChange={(value) => setData("otp", value)}
                >
                    <InputOTPGroup>
                        <InputOTPSlot index={0} />
                        <InputOTPSlot index={1} />
                    </InputOTPGroup>
                    <InputOTPSeparator />
                    <InputOTPGroup>
                        <InputOTPSlot index={2} />
                        <InputOTPSlot index={3} />
                    </InputOTPGroup>
                    <InputOTPSeparator />
                    <InputOTPGroup>
                        <InputOTPSlot index={4} />
                        <InputOTPSlot index={5} />
                    </InputOTPGroup>
                </InputOTP>
                {globalErrors.otp && <FormError message={globalErrors.otp} />}
                {flash?.error && <FormError message={flash?.error} />}

                <Button type="submit" className="w-full" disabled={processing}>
                    {processing && <LoaderIcon className="animate-spin" />}{" "}
                    Verify
                </Button>
            </form>
        </div>
    );
}
