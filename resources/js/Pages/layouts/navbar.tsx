import React from "react";
import {
    HouseWifi,
    Logs,
    CircleUser,
    Swords,
    Wallet,
    GalleryVerticalEnd,
} from "lucide-react";
import { Link } from "@inertiajs/react";

const Nabar = () => {
    return (
        <div className="w-full  z-[999] backdrop-blur-lg">
            <div className="h-16 z-[999] w-full bg-foreground  py-2 px-4 bottom-0 absolute grid grid-cols-5 items-center">
                <Link href={"/peers"} className="flex flex-col items-center">
                    <Logs size={18} color="#8f8f8f" />
                    <span className="text-sm text-[var(--clr-primary-a0)]">
                        Home
                    </span>
                </Link>
                <Link
                    href={route("peers.contents")}
                    className="flex flex-col items-center"
                >
                    <Swords size={18} color="#8f8f8f" />
                    <span className="text-sm text-[var(--clr-surface-a50)]">
                        My Contests
                    </span>
                </Link>
                <Link href={route("peers.global")} className="flex flex-col items-center">
                    <GalleryVerticalEnd size={18} color="#8f8f8f" />
                    <span className="text-sm text-[var(--clr-surface-a50)]">
                       Tournament
                    </span>
                </Link>
                <Link href={route("wallet.index")} className="flex flex-col items-center">
                    <Wallet size={18} color="#8f8f8f" />
                    <span className="text-sm text-[var(--clr-surface-a50)]">
                        Wallet
                    </span>
                </Link>
                <Link href={"#"} className="flex flex-col items-center">
                    <CircleUser size={18} color="#8f8f8f" />
                    <span className="text-sm text-[var(--clr-surface-a50)]">
                        Profile
                    </span>
                </Link>
            </div>
        </div>
    );
};

export default Nabar;
