import { GalleryVerticalEnd, LoaderIcon } from "lucide-react";

import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Link, useForm, usePage } from "@inertiajs/react";
import FormError from "@/components/error";
import { toast } from "sonner";
import React from "react";

interface ResetInterface extends React.ComponentProps<"div"> {
    code: string;
}

export function ResetPasswordForm({
    className,
    code,
    ...props
}: ResetInterface) {
    const { errors: globalErrors, flash } = usePage<{
        flash: { error: string; success: string };
    }>().props;
    const { post, processing, errors, data, setData } = useForm({
        password_confirmation: "",
        password: "",
        otp: code,
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        console.log(data)
        post(route("reset.password.store"));
    };

    // console.log(globalErrors)

    return (
        <div className={cn("flex flex-col gap-6 w-full px-3", className)} {...props}>
            <form onSubmit={submit} className="w-full">
                <div className="flex flex-col gap-6 w-full">
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
                        <h1 className="text-xl font-bold">Reset Password</h1>
                    </div>
                    <div className="flex flex-col gap-3 w-full">
                        <div className="grid gap-1">
                            <Label htmlFor="phone">Password</Label>
                            <Input
                                id="phone"
                                type="password"
                                placeholder="password..."
                                required
                                value={data.password}
                                onChange={(e) =>
                                    setData("password", e.target.value)
                                }
                            />
                            {globalErrors.phone && (
                                <FormError message={globalErrors.phone} />
                            )}
                        </div>
                        <div className="grid gap-1">
                            <Label htmlFor="password">Confirm Password</Label>
                            <Input
                                id="password"
                                type="password"
                                placeholder="password..."
                                required
                                value={data.password_confirmation}
                                onChange={(e) =>
                                    setData(
                                        "password_confirmation",
                                        e.target.value
                                    )
                                }
                            />

                            {globalErrors.password_confirmation && (
                                <FormError message={globalErrors.password_confirmation} />
                            )}
                            {flash?.error && (
                                <FormError message={flash?.error} />
                            )}
                        </div>

                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                        >
                            {processing && (
                                <LoaderIcon className="animate-spin" />
                            )}{" "}
                            Reset Password
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    );
}
