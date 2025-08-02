import React from "react";
import { Head } from "@inertiajs/react";
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
    const sortedUsers = [...peer.users].sort(
        (a, b) => b.total_points - a.total_points
    );
    const currentUser = peer.users.find((u) => u.user.id === 1); // Replace with actual user ID

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
                        <h1 className="text-xl font-bold text-gray-900">
                            {peer.name}
                        </h1>
                        <p className="text-sm text-gray-600">
                            Peer Competition
                        </p>
                    </div>
                    <Badge
                        className={
                            peer.status === "open"
                                ? "bg-green-100 text-green-800"
                                : "bg-gray-100 text-gray-800"
                        }
                    >
                        {peer.status === "open" ? "Active" : peer.status}
                    </Badge>
                </div>

                {/* Peer Stats */}
                <div className="grid grid-cols-2 gap-3">
                    <Card className="bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
                        <CardContent className="p-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-blue-200 rounded-full">
                                    <DollarSign className="w-5 h-5 text-blue-700" />
                                </div>
                                <div>
                                    <p className="text-xs text-blue-600 font-medium">
                                        Entry Fee
                                    </p>
                                    <p className="text-lg font-bold text-blue-900">
                                        ${peer.amount}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="bg-gradient-to-br from-green-50 to-green-100 border-green-200">
                        <CardContent className="p-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-green-200 rounded-full">
                                    <Users className="w-5 h-5 text-green-700" />
                                </div>
                                <div>
                                    <p className="text-xs text-green-600 font-medium">
                                        Participants
                                    </p>
                                    <p className="text-lg font-bold text-green-900">
                                        {peer.users_count}/{peer.limit || "∞"}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Prize Pool */}
                <Card className="bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200">
                    <CardContent className="p-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-purple-600 font-medium">
                                    Total Prize Pool
                                </p>
                                <p className="text-2xl font-bold text-purple-900">
                                    $
                                    {(
                                        parseFloat(peer.amount) *
                                        peer.users_count
                                    ).toFixed(2)}
                                </p>
                            </div>
                            <div className="text-right">
                                <p className="text-xs text-purple-600">
                                    Sharing Ratio
                                </p>
                                <p className="text-lg font-bold text-purple-900">
                                    {peer.sharing_ratio}x
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Current User Status */}
                {currentUser && (
                    <Card className="border-l-4 border-l-blue-500">
                        <CardContent className="p-4">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    <Avatar className="w-10 h-10">
                                        <AvatarImage
                                            src={currentUser.user.avatar}
                                        />
                                        <AvatarFallback className="bg-blue-100 text-blue-700">
                                            {currentUser.user.username
                                                .substring(0, 2)
                                                .toUpperCase()}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <p className="font-semibold text-gray-900">
                                            Your Performance
                                        </p>
                                        <p className="text-sm text-gray-600">
                                            {currentUser.user.username}
                                        </p>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <p className="text-xs text-gray-600">
                                        Total Points
                                    </p>
                                    <p className="text-lg font-bold text-blue-600">
                                        {currentUser.total_points}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Leaderboard */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Trophy className="w-5 h-5 text-yellow-500" />
                            Leaderboard
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-3">
                            {sortedUsers.map((user, index) => (
                                <div
                                    key={user.id}
                                    className={`flex items-center justify-between p-3 rounded-lg border ${getRankColor(
                                        index
                                    )}`}
                                >
                                    <div className="flex items-center gap-3">
                                        <div className="flex items-center justify-center w-8 h-8">
                                            {getRankIcon(index)}
                                        </div>
                                        <Avatar className="w-8 h-8">
                                            <AvatarImage
                                                src={user.user.avatar}
                                            />
                                            <AvatarFallback className="text-xs">
                                                {user.user.username
                                                    .substring(0, 2)
                                                    .toUpperCase()}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <p className="font-medium text-gray-900">
                                                {user.user.username}
                                            </p>
                                            <p className="text-xs text-gray-600">
                                                {user.is_winner
                                                    ? "Winner"
                                                    : "Participant"}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className="text-lg font-bold text-gray-900">
                                            {user.total_points}
                                        </p>
                                        <p className="text-xs text-gray-600">
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
                        <Button className="w-full bg-blue-600 hover:bg-blue-700">
                            <Target className="w-4 h-4 mr-2" />
                            Join This Peer
                        </Button>
                        <p className="text-xs text-gray-600 text-center">
                            Join this peer competition and start earning points!
                        </p>
                    </div>
                )}

                {peer.status === "open" && currentUser && (
                    <Card className="bg-blue-50 border-blue-200">
                        <CardContent className="p-4">
                            <div className="flex items-center gap-3">
                                <TrendingUp className="w-5 h-5 text-blue-600" />
                                <div>
                                    <p className="font-medium text-blue-900">
                                        You're in this peer!
                                    </p>
                                    <p className="text-sm text-blue-700">
                                        Keep betting to earn more points
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {peer.status === "finished" && (
                    <Card className="bg-green-50 border-green-200">
                        <CardContent className="p-4">
                            <div className="flex items-center gap-3">
                                <Trophy className="w-5 h-5 text-green-600" />
                                <div>
                                    <p className="font-medium text-green-900">
                                        Competition Finished!
                                    </p>
                                    <p className="text-sm text-green-700">
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
