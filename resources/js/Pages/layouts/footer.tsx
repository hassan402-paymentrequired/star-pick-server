import { Flame, UserCircle } from "lucide-react";
import React from "react";

const Footer = () => {
    return (
        <div className="w-full border-b z-50 h-12 flex items-center px-2 justify-between">
            <div className="flex items-center gap-1">
                <UserCircle size={17} />
                <span className="text-sm">
                    <strong>Hi,</strong> hassan
                </span>
            </div>

            <div className="">
                <Flame size={40} />
            </div>

            <div className="flex items-center gap-0.5">
                <span className="text-xs">balance</span>
                <span className="text-sm font-bold">0.00</span>
            </div>
        </div>
    );
};

export default Footer;
