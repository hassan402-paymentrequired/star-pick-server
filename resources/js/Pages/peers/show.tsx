import React from "react";
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
    Star,
    ArrowLeft,
} from "lucide-react";
import { PageProps } from "@/types";

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
}

export default function PeerShow({ peer }: PeerShowProps) {
    const {
        auth: { user },
    } = usePage<{ auth: { user: any } }>().props;
    const sortedUsers = [...peer.users].sort(
        (a, b) => b.total_points - a.total_points
    );
    const currentUser = peer.users.find((u) => u.id === user.id); 

    const getRankIcon = (index: number) => {
        switch (index) {
            case 0:
                return <Crown className="w-4 h-4 text-yellow-500" />;
            case 1:
                return <Medal className="w-4 h-4 text-gray-400" />;
            case 2:
                return <Medal className="w-4 h-4 text-amber-600" />;
            default:
                return (
                    <span className="text-sm font-medium text-gray-500">
                        {index + 1}
                    </span>
                );
        }
    };

    const getRankColor = (index: number) => {
        switch (index) {
            case 0:
                return "bg-gradient-to-r from-yellow-50 to-yellow-100 border-yellow-200";
            case 1:
                return "bg-gradient-to-r from-gray-50 to-gray-100 border-gray-200";
            case 2:
                return "bg-gradient-to-r from-amber-50 to-amber-100 border-amber-200";
            default:
                return "bg-white border-gray-200";
        }
    };

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

                {/* Peer Stats */}
                <div className="grid grid-cols-2 gap-3">
                    <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                        <CardContent className="p-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-[var(--clr-primary-a0)] rounded-full">
                                    <DollarSign className="w-5 h-5 text-[var(--clr-light-a0)]" />
                                </div>
                                <div>
                                    <p className="text-xs text-[var(--clr-primary-a0)] font-medium">
                                        Entry Fee
                                    </p>
                                    <p className="text-lg font-bold text-[var(--clr-light-a0)]">
                                        ${peer.amount}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                        <CardContent className="p-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-[var(--clr-primary-a0)] rounded-full">
                                    <Users className="w-5 h-5 text-[var(--clr-light-a0)]" />
                                </div>
                                <div>
                                    <p className="text-xs text-[var(--clr-primary-a0)] font-medium">
                                        Participants
                                    </p>
                                    <p className="text-lg font-bold text-[var(--clr-light-a0)]">
                                        {peer.users_count}/{peer.limit || "∞"}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Prize Pool */}
                <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                    <CardContent className="p-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-[var(--clr-primary-a0)] font-medium">
                                    Total Prize Pool
                                </p>
                                <p className="text-2xl font-bold text-[var(--clr-light-a0)]">
                                    $
                                    {(
                                        parseFloat(peer.amount) *
                                        peer.users_count
                                    ).toFixed(2)}
                                </p>
                            </div>
                            <div className="text-right">
                                <p className="text-xs text-[var(--clr-primary-a0)]">
                                    Sharing Ratio
                                </p>
                                <p className="text-lg font-bold text-[var(--clr-light-a0)]">
                                    {peer.sharing_ratio}x
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Current User Status */}
                {currentUser && (
                    <Card className="border-l-4 border-l-[var(--clr-primary-a0)] bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                        <CardContent className="p-4">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    <Avatar className="w-10 h-10">
                                        <AvatarImage
                                            src={currentUser.avatar}
                                        />
                                        <AvatarFallback className="bg-[var(--clr-primary-a0)] text-[var(--clr-light-a0)]">
                                            {currentUser.username
                                                .substring(0, 2)
                                                .toUpperCase()}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <p className="font-semibold text-[var(--clr-light-a0)]">
                                            Your Performance
                                        </p>
                                        <p className="text-sm text-[var(--clr-surface-a50)]">
                                            {currentUser.username}
                                        </p>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <p className="text-xs text-[var(--clr-surface-a50)]">
                                        Total Points
                                    </p>
                                    <p className="text-lg font-bold text-[var(--clr-primary-a0)]">
                                        {currentUser.total_points}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Leaderboard */}
                <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-[var(--clr-light-a0)]">
                            <Trophy className="w-5 h-5 text-[var(--clr-primary-a0)]" />
                            Leaderboard
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-3">
                            {sortedUsers.map((user, index) => (
                                <div
                                    key={user.id}
                                    className={`flex items-center justify-between p-3 rounded-lg border bg-[var(--clr-surface-a20)] border-[var(--clr-surface-a30)]`}
                                >
                                    <div className="flex items-center gap-3">
                                        <div className="flex items-center justify-center w-8 h-8">
                                            {getRankIcon(index)}
                                        </div>
                                        <Avatar className="w-8 h-8">
                                            <AvatarImage
                                                src={user.avatar}
                                            />
                                            <AvatarFallback className="text-xs bg-[var(--clr-primary-a0)] text-[var(--clr-light-a0)]">
                                                {user.username
                                                    .substring(0, 2)
                                                    .toUpperCase()}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <p className="font-medium text-[var(--clr-light-a0)]">
                                                {user.username}
                                            </p>
                                            <p className="text-xs text-[var(--clr-surface-a50)]">
                                                {user.is_winner
                                                    ? "Winner"
                                                    : "Participant"}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className="text-lg font-bold text-[var(--clr-light-a0)]">
                                            {user.total_points}
                                        </p>
                                        <p className="text-xs text-[var(--clr-surface-a50)]">
                                            points
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                {/* Actions */}
                {peer.status === "open" && !currentUser && (
                    <div className="space-y-3">
                        <Button className="w-full bg-[var(--clr-primary-a0)] hover:bg-[var(--clr-primary-a10)] text-[var(--clr-light-a0)]">
                            <Target className="w-4 h-4 mr-2" />
                            Join This Peer
                        </Button>
                        <p className="text-xs text-[var(--clr-surface-a50)] text-center">
                            Join this peer competition and start earning points!
                        </p>
                    </div>
                )}

                {peer.status === "open" && currentUser && (
                    <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                        <CardContent className="p-4">
                            <div className="flex items-center gap-3">
                                <TrendingUp className="w-5 h-5 text-[var(--clr-primary-a0)]" />
                                <div>
                                    <p className="font-medium text-[var(--clr-light-a0)]">
                                        You're in this peer!
                                    </p>
                                    <p className="text-sm text-[var(--clr-surface-a50)]">
                                        Keep betting to earn more points
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {peer.status === "finished" && (
                    <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                        <CardContent className="p-4">
                            <div className="flex items-center gap-3">
                                <Trophy className="w-5 h-5 text-[var(--clr-primary-a0)]" />
                                <div>
                                    <p className="font-medium text-[var(--clr-light-a0)]">
                                        Competition Finished!
                                    </p>
                                    <p className="text-sm text-[var(--clr-surface-a50)]">
                                        {peer.winner_user_id
                                            ? "Check the leaderboard for results"
                                            : "Results will be announced soon"}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </MainLayout>
    );
}
