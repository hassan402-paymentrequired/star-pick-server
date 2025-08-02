import React from "react";
import { Head } from "@inertiajs/react";
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
} from "lucide-react";
import { PageProps } from "@/types";

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
}

export default function PeersIndex({ peers, recent }: PeersProps) {
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

                {/* Header */}
                <div className="space-y-4">
                    <div className="flex items-center justify-between">
                        <h1 className="text-2xl font-bold">Peers</h1>
                    </div>
                </div>
            
        </MainLayout>
    );
}
