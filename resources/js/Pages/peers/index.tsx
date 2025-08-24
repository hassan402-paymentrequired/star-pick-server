import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Pages/layouts/main-layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
    Plus,
    Users,
    Trophy,
    Clock,
    Lock,
    Globe,
    TrendingUp,
    Crown,
    DollarSign,
    Target,
    Sword,
    CupSoda,
    HandCoins,
    ArrowDownLeftSquareIcon,
    ArrowDownRightSquareIcon,
} from "lucide-react";
import { PageProps } from "@/types";
import { Swiper, SwiperSlide } from "swiper/react";
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/components/ui/collapsible";

// Import Swiper styles
import "swiper/css";
import { Avatar } from "@radix-ui/react-avatar";
import { AvatarFallback } from "@/components/ui/avatar";

interface Peer {
    id: number;
    peer_id: string;
    name: string;
    amount: string;
    private: boolean;
    limit: number;
    sharing_ratio: number;
    status: "open" | "closed" | "finished";
    winner_user_id?: number;
    created_by: {
        id: number;
        username: string;
    };
    users_count: number;
    created_at: string;
}

interface PeersProps extends PageProps {
    peers: Peer[];
    recent: Peer[];
    tournament: any;
}

export default function PeersIndex({ peers, recent, tournament }: PeersProps) {
    // console.log(tournament);

    const getStatusColor = (status: string) => {
        switch (status) {
            case "open":
                return "bg-green-100 text-green-800";
            case "closed":
                return "bg-yellow-100 text-yellow-800";
            case "finished":
                return "bg-blue-100 text-blue-800";
            default:
                return "bg-gray-100 text-gray-800";
        }
    };

    const getStatusIcon = (status: string) => {
        switch (status) {
            case "open":
                return <Globe className="w-4 h-4" />;
            case "closed":
                return <Lock className="w-4 h-4" />;
            case "finished":
                return <Trophy className="w-4 h-4" />;
            default:
                return <Clock className="w-4 h-4" />;
        }
    };

    return (
        <MainLayout>
            <Head title="Peers" />

            <div className="mt-2 space-y-4 p-3">
                {/* Global Challenge Card */}
                <Card className="relative rounded   p-0 py-2 overflow-hidden">
                    {/* Background Image */}
                    <div
                        className="absolute inset-0 z-0"
                        style={{
                            backgroundImage:
                                "url('/assets/images/global-challenge-bg.jpg')",
                            backgroundSize: "cover",
                            backgroundPosition: "center",
                            // opacity: 0.7,
                            pointerEvents: "none",
                        }}
                        aria-hidden="true"
                    />
                    <CardHeader className="relative z-10 pb-0">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-3xl capitalize font-semibold text-white">
                                    {tournament?.name}
                                </h3>
                                <p className="text-foreground">
                                    Join other users in todays tournament{" "}
                                    <Sword size={16} />
                                </p>
                            </div>
                            <div className="text-right">
                                <div className=" text-foreground font-bold">
                                    ₦{tournament.amount}
                                </div>
                                <div className="text-foreground">
                                    Prize Pool
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className="pt-0 relative z-10">
                        <div className="grid grid-cols-2 gap-3">
                            <Link href={route("tournament.index")} prefetch>
                                <Button className="w-full text-foreground tracking-wider font-bold ">
                                    Join {tournament.name}
                                </Button>
                            </Link>
                            <Link href={route("peers.create")} prefetch>
                                <Button
                                    variant="outline"
                                    className="w-full tracking-wider text-foreground font-bold"
                                >
                                    Create Peer
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                {/* Recent Peers Section */}
                <div className="space-y-3">
                    <div className="flex items-center justify-between">
                        <h3 className="text-lg tracking-wider font-semibold text-[var(--clr-light-a0)]">
                            Recent Peers
                        </h3>
                    </div>

                    <div className="mb-10">
                        <Swiper
                            spaceBetween={10}
                            slidesPerView={1.2}
                            className="w-full"
                            autoplay={{
                                delay: 3000,
                                disableOnInteraction: false,
                            }}
                            loop={true}
                            pagination={{
                                clickable: true,
                            }}
                            navigation
                            speed={1000}
                        >
                            {recent?.map((peer) => (
                                <SwiperSlide key={peer.id}>
                                    <div className="p-1 relative z-0 backdrop-blur-[1px] bg-white/10  border rounded-sm">
                                        <Card className="w-full z-50 bg-default/10 rounded p-0 border-input transition-all duration-300 cursor-pointer group">
                                            <CardContent className="p-3">
                                                {/* Header */}
                                                <div className="flex items-start justify-between mb-3">
                                                    <div className="flex-1">
                                                        <div className="flex items-center gap-2 mb-1">
                                                            <h4 className="font-semibold capitalize text-muted-white text-sm truncate">
                                                                {peer.name}
                                                            </h4>
                                                        </div>
                                                        <p className="text-xs text-muted">
                                                            by{" "}
                                                            @{
                                                                peer.created_by
                                                                    .username
                                                            }
                                                        </p>
                                                    </div>
                                                    <Badge
                                                        className={`text-xs px-2 py-1 text-default rounded bg-background tracking-wider`}
                                                    >
                                                        ₦
                                                        {Number(
                                                            peer.amount
                                                        ).toFixed()}
                                                    </Badge>
                                                </div>

                                                {/* Prize Pool */}
                                                <div className="p-2 mb-3 iteme-center w-full justify-center">
                                                    <div className="*:data-[slot=avatar]:ring-background flex -space-x-2 *:data-[slot=avatar]:ring-2 *:data-[slot=avatar]:grayscale">
                                                        {peer?.users_count >
                                                        0 ? (
                                                            Array.from({
                                                                length: peer.users_count,
                                                            }).map((_, idx) => (
                                                                <Avatar
                                                                    key={idx}
                                                                    className="rounded"
                                                                >
                                                                    <AvatarFallback className="rounded size-7">
                                                                        {idx +
                                                                            1}
                                                                    </AvatarFallback>
                                                                </Avatar>
                                                            ))
                                                        ) : (
                                                            <span className="text-xs text-center text-muted">
                                                                No one has
                                                                joined yet
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Action Button */}
                                                <div className="grid grid-cols-2 gap-3">
                                                    <Link
                                                        href={route(
                                                            "peers.show",
                                                            {
                                                                peer: peer.id,
                                                            }
                                                        )}
                                                        prefetch
                                                    >
                                                        <Button
                                                            className="w-full hover:bg-blue-600 text-foreground text-sm font-medium"
                                                            size="sm"
                                                        >
                                                            <Target className="w-3 h-3 mr-1" />
                                                            View Peer
                                                        </Button>
                                                    </Link>
                                                    <Link
                                                        href={route(
                                                            "peers.join",
                                                            {
                                                                peer: peer.id,
                                                            }
                                                        )}
                                                        prefetch
                                                    >
                                                        <Button
                                                            className="w-full hover:bg-blue-600 text-foreground text-sm font-medium"
                                                            size="sm"
                                                        >
                                                            Join Peer
                                                            <ArrowDownRightSquareIcon className="w-3 h-3 mr-1 transition duration-100 group-hover:-rotate-45" />
                                                        </Button>
                                                    </Link>
                                                </div>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </SwiperSlide>
                            ))}
                        </Swiper>
                    </div>
                </div>

                <div className="space-y-3 mt-3">
                    <div className="flex items-center justify-between">
                        <h3 className="text-lg flex font-semibold text-[var(--clr-light-a0)]">
                            <CupSoda /> Top Peers
                        </h3>
                    </div>

                    <div className="flex flex-col mt-2">
                        {(peers.data || []).map((peer, i) => (
                            <Card className="mb-3 p-0 group bg-background/10 border ring ring-background rounded" key={i}>
                                <Collapsible>
                                    <CollapsibleTrigger className="w-full flex items-center justify-between p-2 cursor-pointer hover:bg-[var(--clr-surface-a10)] transition rounded">
                                        <div className="flex items-center gap-2">
                                            <Avatar className="w-8 h-8 rounded-full bg-[var(--clr-surface-a20)] flex items-center justify-center">
                                                <AvatarFallback className="rounded ring-2 shadow ring-foreground">
                                                    {peer.name.substring(0,2).toUpperCase()}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div className="items-start flex flex-col">
                                                <div className="font-semibold text-muted-white text-base">
                                                    {peer.name}
                                                </div>
                                                <div className="text-xs text-muted-white">
                                                    by @
                                                    {peer.created_by.username}
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
                                                <div className="size-10 rounded-full ring ring-background shadow bg-foreground flex items-center justify-center">
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
                                                        Entry Fee
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
                                        {/* <div className="px-4 py-3 border-t border-border grid grid-cols-1 md:grid-cols-2 gap-4">

                                        </div> */}
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
                                                    variant="outline"
                                                >
                                                    <Target className="w-3 h-3 mr-1" />
                                                    View Peer
                                                </Button>
                                            </Link>
                                            <Link
                                                href={route("peers.join", {
                                                    peer: peer.id,
                                                })}
                                                className="w-full"
                                                prefetch
                                            >
                                                <Button
                                                    className="w-full text-foreground text-sm font-medium"
                                                    size="sm"
                                                >
                                                    Join Peer
                                                    <ArrowDownRightSquareIcon className="w-3 h-3 mr-1 group-hover:-rotate-45 transition duration-100" />
                                                </Button>
                                            </Link>
                                        </div>
                                    </CollapsibleContent>
                                </Collapsible>
                            </Card>
                        ))}
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
