import React, { useState } from "react";
import { Head, usePage } from "@inertiajs/react";
import MainLayout from "@/Pages/layouts/main-layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import {
    Trophy,
    Users,
    DollarSign,
    Target,
    TrendingUp,
    Crown,
    Medal,
    ArrowLeft,
    ChevronDown,
    ChevronUp,
} from "lucide-react";
import { PageProps } from "@/types";
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/components/ui/collapsible";

interface PeerUser {
    id: number;
    user: {
        id: number;
        username: string;
        avatar?: string;
    };
    total_points: number;
    is_winner: boolean;
    created_at: string;
}

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
    users: PeerUser[];
    users_count: number;
    created_at: string;
}

interface PeerShowProps extends PageProps {
    peer: Peer;
    users: any[];
}

export default function PeerShow({ peer, users }: PeerShowProps) {
    const {
        auth: { user },
    } = usePage<{ auth: { user: any } }>().props;

    const sortedUsers = [...users].sort(
        (a, b) => b.total_points - a.total_points
    );
    const currentUser = users.find((u) => u.id === user.id);

    // Collapse state for each user row
    const [openSquad, setOpenSquad] = useState<number | null>(null);

    const getRankIcon = (index: number) => {
        switch (index) {
            case 0:
                return <Crown className="w-5 h-5 text-yellow-500" />;
            case 1:
                return <Medal className="w-5 h-5 text-gray-400" />;
            case 2:
                return <Medal className="w-5 h-5 text-amber-600" />;
            default:
                return (
                    <span className="text-base font-bold text-gray-500">
                        {index + 1}
                    </span>
                );
        }
    };

    console.log(users);

    return (
        <MainLayout>
            <Head title={`Peer: ${peer.name}`} />

            <div className="space-y-4">
                {/* Header */}
                <div className="flex items-center gap-3">
                    <Button variant="ghost" size="sm" className="p-2">
                        <ArrowLeft className="w-4 h-4" />
                    </Button>
                    <div className="flex-1">
                        <h1 className="text-xl font-bold text-[var(--clr-light-a0)]">
                            {peer.name}
                        </h1>
                        <p className="text-sm text-[var(--clr-surface-a50)]">
                            Peer Competition
                        </p>
                    </div>
                    <Badge
                        className={
                            peer.status === "open"
                                ? "bg-[var(--clr-primary-a0)] text-[var(--clr-light-a0)]"
                                : "bg-[var(--clr-surface-a20)] text-[var(--clr-surface-a50)]"
                        }
                    >
                        {peer.status === "open" ? "Active" : peer.status}
                    </Badge>
                </div>

                {/* table */}

                {users.length &&
                    users.map((user) => (
                        <Card className="mb-3 p-0 bg-background border border-[var(--clr-surface-a20)] shadow-sm rounded">
                            <Collapsible>
                                <CollapsibleTrigger className="w-full flex items-center justify-between p-2 cursor-pointer hover:bg-[var(--clr-surface-a10)] transition rounded">
                                    <div className="flex items-center gap-2">
                                        <Avatar className="w-8 h-8 rounded-full bg-[var(--clr-surface-a20)] flex items-center justify-center">
                                            <AvatarFallback className="rounded">
                                                {user.username.substring(0, 2)}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div className="items-start flex flex-col">
                                            <div className="font-semibold text-muted-white text-base">
                                                {user.username}
                                            </div>
                                            <div className="text-xs text-muted-white">
                                                by @{peer.created_by.username}
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
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-border bg-transparent text-muted rounded shadow">
                                            <thead className="bg-[var(--clr-surface-a10)]">
                                                <tr>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        #
                                                    </th>

                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Player
                                                    </th>

                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        type
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Position
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Goals
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Shot On
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        GK Save
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Assists
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Tackle
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Shots
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Yellow Card
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        Red Card
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium text-muted-white uppercase tracking-wider">
                                                        TP %
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody className=" divide-y divide-border bg-transparent">
                                                {user.squads &&
                                                user.squads.length > 0 ? (
                                                    user.squads.map((squad, idx) => (
                                                        <>
                                                        <tr key={squad.id + idx + "-star"}>
                                                            <td colSpan={12} className=" text-sm text-muted px-3 py-1">
                                                                Star {idx + 1} ⚽
                                                            </td>
                                                        </tr>

                                                            <tr key={squad.id + "-main"}>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.main_player?.did_play ? "⚽" : "🪑"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.main_player?.name}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    Main
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.main_player?.position}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.main_player?.statistics?.goals ?? 0}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.main_player?.statistics?.shots_on ?? 0}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.main_player?.statistics?.goals_saves ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.main_player?.statistics?.assists ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {squad.main_player?.statistics?.tackles_total ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {squad.main_player?.statistics?.shots ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {squad.main_player?.statistics?.cards_yellow ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {squad.main_player?.statistics?.cards_red ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {/* Card between the stars */}
                                                                    {squad.main_player?.statistics?.cards_yellow || squad.main_player?.statistics?.cards_red
                                                                        ? (
                                                                            <>
                                                                                {squad.main_player?.statistics?.cards_yellow > 0 && (
                                                                                    <span className="inline-block mr-1 text-yellow-500">🟨</span>
                                                                                )}
                                                                                {squad.main_player?.statistics?.cards_red > 0 && (
                                                                                    <span className="inline-block text-red-500">🟥</span>
                                                                                )}
                                                                            </>
                                                                        )
                                                                        : 0}
                                                                </td>
                                                            </tr>
                                                            <tr key={squad.id + "-sub"}>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.sub_player?.did_play ? "⚽" : "🪑"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.sub_player?.name}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    Sub
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.sub_player?.position}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.sub_player?.statistics?.goals ?? 0}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.sub_player?.statistics?.shots_on ?? 0}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.sub_player?.statistics?.goals_saves ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm text-muted">
                                                                    {squad.sub_player?.statistics?.assists ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {squad.sub_player?.statistics?.tackles_total ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {squad.sub_player?.statistics?.shots ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {squad.sub_player?.statistics?.cards_yellow ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {squad.sub_player?.statistics?.cards_red ?? "0"}
                                                                </td>
                                                                <td className="px-3 py-2 text-sm ">
                                                                    {/* Card between the stars */}
                                                                    {squad.sub_player?.statistics?.cards_yellow || squad.sub_player?.statistics?.cards_red
                                                                        ? (
                                                                            <>
                                                                                {squad.sub_player?.statistics?.cards_yellow > 0 && (
                                                                                    <span className="inline-block mr-1 text-yellow-500">🟨</span>
                                                                                )}
                                                                                {squad.sub_player?.statistics?.cards_red > 0 && (
                                                                                    <span className="inline-block text-red-500">🟥</span>
                                                                                )}
                                                                            </>
                                                                        )
                                                                        : 0}
                                                                </td>
                                                            </tr>
                                                        </>
                                                    ))
                                                ) : (
                                                    <tr>
                                                        <td
                                                            colSpan={9}
                                                            className="px-3 py-4 text-center text-gray-400"
                                                        >
                                                            No squad data
                                                            available.
                                                        </td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                </CollapsibleContent>
                            </Collapsible>
                        </Card>
                    ))}
            </div>
        </MainLayout>
    );
}
