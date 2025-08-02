import { usePage } from "@inertiajs/react";
import { Flame, UserCircle } from "lucide-react";
import React from "react";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";

const Footer = () => {
    const {
        auth: { user },
    } = usePage<{ auth: { user: any } }>().props;

    return (
        <div className="w-full border-b-[var(--clr-surface-a20)] bg-[var(--clr-surface-a10)] z-50 h-12 flex items-center px-2 justify-between">
            <div className="flex items-center gap-1">
                <Avatar className=" rounded">
                    <AvatarImage src="https://github.com/shadcn.png" />
                    <AvatarFallback className="uppercase rounded">
                        {user.username.substring(0, 2)}
                    </AvatarFallback>
                </Avatar>
                <span className="text-sm text-[var(--clr-light-a0)]">
                    <strong>Hi,</strong> {user.username}
                </span>
            </div>

            <div className="">
                <Flame size={40} color="var(--clr-primary-a0)" />
            </div>

            <div className="flex items-center gap-0.5 rounded-full bg-[var(--clr-primary-a0)] px-2 py-1">
                <span className="text-xs text-[var(--clr-surface-a0)]">
                    balance:
                </span>
                <span className="text-sm font-bold text-[var(--clr-surface-a0)]">
                    {user.wallet.balance}
                </span>
            </div>
        </div>
    );
};

export default Footer;
