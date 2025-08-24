import React, { useEffect, useState } from "react";
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import { Search } from "lucide-react";
import { Input } from "./input";
import { Button } from "./button";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Card } from "@/components/ui/card";
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/components/ui/collapsible";
import { Link } from "@inertiajs/react";
import { HandCoins, Target, Users } from "lucide-react";

const SearchPeer = () => {
    const [search, setSearch] = useState("");
    const [peers, setPeers] = useState([]);

    useEffect(() => {
        setTimeout(async () => {
            await handleSearch(search);
        }, 500);
    }, [search]);

    const handleSearch = async (searchQuery: string) => {
        const res = await fetch(route("peers.search", { search }));
        const result = await res.json();
        setPeers(result.peers);
    };

    return (
        <Dialog>
            <DialogTrigger asChild>
                <div className="flex px-2  items-center gap-1 shadow rounded focus:ring-1 focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]">
                    <Search size={17} color="#aaa" />
                    <Input
                        className="border-none bg-transparent px-0 shadow-none focus:ring-0 focus:shadow-none focus-visible:border-none focus-visible:ring-0 "
                        placeholder="peer code"
                    />
                </div>
            </DialogTrigger>

            <DialogContent className="sm:max-w-[425px] p-1.5 backdrop-blur-md border bg-white">
                <div className="p-4 rounded-lg shadow-sm bg-default/10">
                    <DialogHeader>
                        <DialogTitle>Search Peer</DialogTitle>
                        <DialogDescription>
                            Enter your invite code to search for peer
                        </DialogDescription>
                    </DialogHeader>
                    <div className="flex px-2  items-center gap-1 shadow rounded focus:ring-1 focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]">
                        <Search size={17} color="#aaa" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="border-none bg-transparent px-0 shadow-none focus:ring-0 focus:shadow-none focus-visible:border-none focus-visible:ring-0 "
                            placeholder="code..."
                        />
                    </div>

                    <div className="my-3">
                        {peers.length > 0 &&
                            peers.map((peer: any) => (
                                <Card className="mb-3 p-0 bg-background/10 shadow border rounded">
                                    <Collapsible open={true}>
                                        <CollapsibleTrigger className="w-full flex items-center justify-between p-2 cursor-pointer hover:bg-[var(--clr-surface-a10)] transition rounded">
                                            <div className="flex items-center gap-2">
                                                <Avatar className="w-8 h-8 rounded-full bg-[var(--clr-surface-a20)] flex items-center justify-center">
                                                    <AvatarFallback className="uppercase rounded-full ring ring-background shadow flex items-center justify-center">
                                                        {peer.name.substring(
                                                            0,
                                                            2
                                                        )}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <div className="items-start flex flex-col">
                                                    <div className="font-semibold text-muted-white text-base">
                                                        {peer.name}
                                                    </div>
                                                    <div className="text-xs text-muted-white">
                                                        by @
                                                        {
                                                            peer.created_by
                                                                .username
                                                        }
                                                    </div>
                                                </div>
                                            </div>
                                            <span className="font-medium text-muted">
                                                {new Date(
                                                    peer.created_at
                                                ).toLocaleDateString()}
                                            </span>
                                        </CollapsibleTrigger>
                                        <CollapsibleContent>
                                            <div className="px-4  py-3 border-t border-border grid grid-cols-2 gap-4">
                                                <div className="flex items-center gap-2">
                                                    <div className="size-10 rounded-full ring ring-background shadow flex items-center justify-center">
                                                        <Users size={18} />
                                                    </div>
                                                    <div className="flex flex-col items-start">
                                                        <small className="text-muted">
                                                            Entries
                                                        </small>
                                                        <span className="text-muted-white">
                                                            {peer.users_count}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <div className="size-10 rounded-full ring ring-background shadow flex items-center justify-center">
                                                        <HandCoins size={18} />
                                                    </div>
                                                    <div className="flex flex-col items-start">
                                                        <small className="text-muted">
                                                            Fees
                                                        </small>
                                                        <span className="text-muted-white">
                                                            ₦
                                                            {Number(
                                                                peer.amount
                                                            ).toFixed()}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="px-4 py-3 flex gap-3 border-t border-border">
                                                <Link
                                                    href={route("peers.show", {
                                                        peer: peer.id,
                                                    })}
                                                    className="w-full"
                                                    prefetch
                                                >
                                                    <Button
                                                        className="w-full  text-sm font-medium"
                                                        size="sm"
                                                    >
                                                        <Target className="w-3 h-3 mr-1" />
                                                        View Peer
                                                    </Button>
                                                </Link>
                                            </div>
                                        </CollapsibleContent>
                                    </Collapsible>
                                </Card>
                            ))}
                    </div>
                    <div>
                        {peers.length === 0 && (
                            <div className="flex justify-center py-8">
                                <div className=" p-6 flex flex-col items-center max-w-xs">
                                    <span className="text-4xl mb-2 animate-bounce">
                                        🤷‍♂️
                                    </span>
                                    <div className="text-center text-muted mb-3">
                                        No peer was found with the provided code
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                    <DialogFooter>
                        <DialogClose asChild>
                            <Button variant="outline">Cancel</Button>
                        </DialogClose>
                    </DialogFooter>
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default SearchPeer;
