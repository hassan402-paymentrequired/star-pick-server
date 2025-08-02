import React from "react";
import { HouseWifi, Logs, CircleUser, Swords } from "lucide-react";
import { Link } from "@inertiajs/react";

const Nabar = () => {
    return (
        <div className="h-16 w-full py-2 px-4 bg-[var(--clr-surface-a10)] border-t-[var(--clr-surface-a20)] bottom-0 absolute grid grid-cols-5 items-center">
            <Link href={"/peers"} className="flex flex-col items-center">
                <Logs size={20} color="var(--clr-primary-a0)" />
                <span className="text-sm text-[var(--clr-primary-a0)]">
                    Home
                </span>
            </Link>
            <Link href={"#"} className="flex flex-col items-center">
                <Swords size={20} color="var(--clr-surface-a50)" />
                <span className="text-sm text-[var(--clr-surface-a50)]">
                    My Contents
                </span>
            </Link>
            <Link href={"#"} className="flex flex-col items-center">
                <HouseWifi size={20} color="var(--clr-surface-a50)" />
                <span className="text-sm text-[var(--clr-surface-a50)]">
                    bookings
                </span>
            </Link>
            <Link href={"#"} className="flex flex-col items-center">
                <HouseWifi size={20} color="var(--clr-surface-a50)" />
                <span className="text-sm text-[var(--clr-surface-a50)]">
                    Wallet
                </span>
            </Link>
            <Link href={"#"} className="flex flex-col items-center">
                <CircleUser size={20} color="var(--clr-surface-a50)" />
                <span className="text-sm text-[var(--clr-surface-a50)]">
                    Profile
                </span>
            </Link>
        </div>
    );
};

export default Nabar;
