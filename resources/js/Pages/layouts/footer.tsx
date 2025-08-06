import { Link, usePage } from "@inertiajs/react";
import { AlertOctagon, Flame, GalleryVerticalEnd, Plus, UserCircle } from "lucide-react";
import React from "react";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";

const Footer = () => {
    const {
        auth: { user },
    } = usePage<{ auth: { user: any } }>().props;

    return (
        <div className="w-full rounded-bl rounded-br opacity-70 bg-primary fixed top-0 left-1/2 -translate-x-1/2 max-w-md z-50 h-12 flex items-center px-2 justify-between">
            <div className="">
                <GalleryVerticalEnd size={40} color="#fff" />
            </div>

            <div className="flex items-center gap-2">
                <div className="flex  items-center gap-0.5 rounded bg-[var(--clr-primary-a0)] px-2 py-1">
                    <span className="text-sm text-[var(--clr-surface-a0)]">
                        Bal:
                    </span>
                    <span className="text-sm text-[var(--clr-surface-a0)]">
                        {user.wallet.balance}
                    </span>
                </div>
                <Link
                    href={"/wallet"}
                    className="text-sm text-[var(--clr-surface-a0)] bg-[var(--clr-primary-a0)] rounded-full p-1"
                >
                    <Plus size={20} color="var(--clr-surface-a0)" />
                </Link>
            </div>
        </div>
    );
};

export default Footer;
