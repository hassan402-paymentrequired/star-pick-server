import { usePage } from "@inertiajs/react";
import { Flame, UserCircle } from "lucide-react";
import React from "react";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";

const Footer = () => {
    const {
        auth: { user },
    } = usePage<{ auth: { user: any } }>().props;

    return (
        <div className="w-full border-b z-50 h-12 flex items-center px-2 justify-between">
            <div className="flex items-center gap-1">
                <Avatar className=" rounded">
                    <AvatarImage src="https://github.com/shadcn.png" />
                    <AvatarFallback className="uppercase rounded">{user.username.substring(0,2)}</AvatarFallback>
                </Avatar>
                <span className="text-sm">
                    <strong>Hi,</strong> {user.username}
                </span>
            </div>

            <div className="">
                <Flame size={40} />
            </div>

            <div className="flex items-center gap-0.5 rounded-full bg-green-200 px-2 py-1">
                <span className="text-xs">balance:</span>
                <span className="text-sm font-bold">{user.wallet.balance}</span>
            </div>
        </div>
    );
};

export default Footer;
