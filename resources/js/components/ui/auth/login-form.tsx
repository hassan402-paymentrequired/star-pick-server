import { GalleryVerticalEnd, LoaderIcon } from "lucide-react";

import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Link, useForm, usePage } from "@inertiajs/react";
import FormError from "@/components/error";
import { toast } from "sonner";

interface LoginFormData {
    phone: string;
    password: string;
}

export function LoginForm({
    className,
    ...props
}: React.ComponentProps<"div">) {
    const { errors: globalErrors, flash } = usePage<{flash: {error: string, success: string}}>().props;
    const { post, processing, errors, data, setData } = useForm({
        phone: "",
        password: "",
    });

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route("login.store"), {
            onSuccess: () => {
                if (flash?.success) {
                    toast.error(flash?.success);
                }
            },
            onError: () => {
                toast.error(globalErrors?.phone || globalErrors?.password);
            },
            onFinish: () => {
                if (flash?.error) {
                    toast.error(flash?.error);
                }
                if (flash?.success) {
                    toast.error(flash?.success);
                }
            },
            preserveScroll: true,
        });
    };

    console.log(globalErrors, flash);

    return (
        <div className={cn("flex flex-col gap-6", className)} {...props}>
            <form onSubmit={submit}>
                <div className="flex flex-col gap-6">
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
                            Welcome to Starpick.
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
                    <div className="flex flex-col gap-3">
                        <div className="grid gap-1">
                            <Label htmlFor="phone">Phone</Label>
                            <Input
                                id="phone"
                                type="text"
                                placeholder="+234 000 000 0000"
                                required
                                value={data.phone}
                                onChange={(e) =>
                                    setData("phone", e.target.value)
                                }
                            />
                            {globalErrors.phone && (
                                <FormError message={globalErrors.phone} />
                            )}
                        </div>
                        <div className="grid gap-1">
                            <Label htmlFor="password">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                placeholder="password..."
                                required
                                value={data.password}
                                onChange={(e) =>
                                    setData("password", e.target.value)
                                }
                            />
                            {globalErrors.password && (
                                <FormError message={globalErrors.password} />
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
                            Login
                        </Button>
                    </div>
                    <div className="after:border-border relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t">
                        <span className="bg-background text-muted-foreground relative z-10 px-2">
                            Or
                        </span>
                    </div>
                    <div className="grid">
                        <Button
                            variant="outline"
                            type="button"
                            className="w-full"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"
                                    fill="currentColor"
                                />
                            </svg>
                            Continue with Google
                        </Button>
                    </div>
                </div>
            </form>
            <div className="text-muted-foreground *:[a]:hover:text-primary text-center text-xs text-balance *:[a]:underline *:[a]:underline-offset-4">
                By clicking continue, you agree to our{" "}
                <a href="#">Terms of Service</a> and{" "}
                <a href="#">Privacy Policy</a>.
            </div>
        </div>
    );
}
