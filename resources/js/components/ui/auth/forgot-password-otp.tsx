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

export function ForgotPasswordOtp() {
    const { errors: globalErrors, flash } = usePage<{ flash: { success: string; error: string } }>().props;
    const { data, setData, processing, post } = useForm({
        otp: "",
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route("forgot.password.store"));
    };

    return (
        <div className="flex flex-col gap-6 p-3 items-center justify-center">
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
                <h1 className="text-xl text-center font-bold">
                    Confirm the code sent to your phone no to reset your
                    password.
                </h1>
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
