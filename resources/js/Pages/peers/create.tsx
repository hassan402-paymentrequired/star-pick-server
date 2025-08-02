import React, { useState } from "react";
import { Head, useForm } from "@inertiajs/react";
import MainLayout from "@/Pages/layouts/main-layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import {
    ArrowLeft,
    Users,
    DollarSign,
    Lock,
    Globe,
    Target,
    Info,
} from "lucide-react";
import { PageProps } from "@/types";

interface CreatePeerProps extends PageProps {
    user: {
        wallet: {
            balance: string;
        };
    };
}

export default function CreatePeer({ user }: CreatePeerProps) {
    const { data, setData, post, processing, errors } = useForm({
        name: "",
        amount: "",
        limit: "",
        sharing_ratio: "1",
        private: false,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post("/peers");
    };

    const calculatePrizePool = () => {
        const amount = parseFloat(data.amount) || 0;
        const limit = parseInt(data.limit) || 0;
        return (amount * limit).toFixed(2);
    };

    return (
        <MainLayout>
            <Head title="Create Peer" />

            <div className="space-y-4">
                {/* Header */}
                <div className="flex items-center gap-3">
                    <Button variant="ghost" size="sm" className="p-2">
                        <ArrowLeft className="w-4 h-4" />
                    </Button>
                    <div>
                        <h1 className="text-xl font-bold text-[var(--clr-light-a0)]">
                            Create Peer
                        </h1>
                        <p className="text-sm text-[var(--clr-surface-a50)]">
                            Start a new betting competition
                        </p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                    {/* Basic Info */}
                    <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2 text-[var(--clr-light-a0)]">
                                <Target className="w-5 h-5 text-[var(--clr-primary-a0)]" />
                                Basic Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <Label
                                    htmlFor="name"
                                    className="text-[var(--clr-light-a0)]"
                                >
                                    Peer Name
                                </Label>
                                <Input
                                    id="name"
                                    type="text"
                                    placeholder="Enter peer name"
                                    value={data.name}
                                    onChange={(e) =>
                                        setData("name", e.target.value)
                                    }
                                    className={
                                        errors.name ? "border-red-500" : ""
                                    }
                                />
                                {errors.name && (
                                    <p className="text-sm text-red-600 mt-1">
                                        {errors.name}
                                    </p>
                                )}
                            </div>

                            <div>
                                <Label
                                    htmlFor="amount"
                                    className="text-[var(--clr-light-a0)]"
                                >
                                    Entry Fee ($)
                                </Label>
                                <Input
                                    id="amount"
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    value={data.amount}
                                    onChange={(e) =>
                                        setData("amount", e.target.value)
                                    }
                                    className={
                                        errors.amount ? "border-red-500" : ""
                                    }
                                />
                                {errors.amount && (
                                    <p className="text-sm text-red-600 mt-1">
                                        {errors.amount}
                                    </p>
                                )}
                            </div>

                            <div>
                                <Label
                                    htmlFor="limit"
                                    className="text-[var(--clr-light-a0)]"
                                >
                                    Player Limit
                                </Label>
                                <Input
                                    id="limit"
                                    type="number"
                                    placeholder="Leave empty for unlimited"
                                    value={data.limit}
                                    onChange={(e) =>
                                        setData("limit", e.target.value)
                                    }
                                    className={
                                        errors.limit ? "border-red-500" : ""
                                    }
                                />
                                {errors.limit && (
                                    <p className="text-sm text-red-600 mt-1">
                                        {errors.limit}
                                    </p>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Settings */}
                    <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2 text-[var(--clr-light-a0)]">
                                <Info className="w-5 h-5 text-[var(--clr-primary-a0)]" />
                                Settings
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <Label
                                    htmlFor="sharing_ratio"
                                    className="text-[var(--clr-light-a0)]"
                                >
                                    Sharing Ratio
                                </Label>
                                <Input
                                    id="sharing_ratio"
                                    type="number"
                                    step="0.1"
                                    placeholder="1.0"
                                    value={data.sharing_ratio}
                                    onChange={(e) =>
                                        setData("sharing_ratio", e.target.value)
                                    }
                                    className={
                                        errors.sharing_ratio
                                            ? "border-red-500"
                                            : ""
                                    }
                                />
                                <p className="text-xs text-[var(--clr-surface-a50)] mt-1">
                                    How much of the prize pool the winner gets
                                    (1.0 = 100%)
                                </p>
                                {errors.sharing_ratio && (
                                    <p className="text-sm text-red-600 mt-1">
                                        {errors.sharing_ratio}
                                    </p>
                                )}
                            </div>

                            <div className="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    id="private"
                                    checked={data.private}
                                    onChange={(e) =>
                                        setData("private", e.target.checked)
                                    }
                                    className="rounded border-gray-300"
                                />
                                <Label
                                    htmlFor="private"
                                    className="flex items-center gap-2 text-[var(--clr-light-a0)]"
                                >
                                    <Lock className="w-4 h-4" />
                                    Private Peer
                                </Label>
                            </div>
                            <p className="text-xs text-[var(--clr-surface-a50)]">
                                Private peers are only visible to invited users
                            </p>
                        </CardContent>
                    </Card>

                    {/* Preview */}
                    <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-primary-a0)]">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2 text-[var(--clr-primary-a0)]">
                                <Globe className="w-5 h-5" />
                                Peer Preview
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <div className="flex items-center justify-between">
                                <span className="text-sm text-[var(--clr-primary-a0)]">
                                    Name:
                                </span>
                                <span className="font-medium text-[var(--clr-light-a0)]">
                                    {data.name || "Your Peer Name"}
                                </span>
                            </div>

                            <div className="flex items-center justify-between">
                                <span className="text-sm text-[var(--clr-primary-a0)]">
                                    Entry Fee:
                                </span>
                                <span className="font-medium text-[var(--clr-light-a0)]">
                                    ${data.amount || "0.00"}
                                </span>
                            </div>

                            <div className="flex items-center justify-between">
                                <span className="text-sm text-[var(--clr-primary-a0)]">
                                    Player Limit:
                                </span>
                                <span className="font-medium text-[var(--clr-light-a0)]">
                                    {data.limit || "Unlimited"}
                                </span>
                            </div>

                            <div className="flex items-center justify-between">
                                <span className="text-sm text-[var(--clr-primary-a0)]">
                                    Sharing Ratio:
                                </span>
                                <span className="font-medium text-[var(--clr-light-a0)]">
                                    {data.sharing_ratio || "1"}x
                                </span>
                            </div>

                            <div className="flex items-center justify-between">
                                <span className="text-sm text-[var(--clr-primary-a0)]">
                                    Privacy:
                                </span>
                                <Badge
                                    className={
                                        data.private
                                            ? "bg-red-100 text-red-800"
                                            : "bg-[var(--clr-primary-a0)] text-[var(--clr-light-a0)]"
                                    }
                                >
                                    {data.private ? "Private" : "Public"}
                                </Badge>
                            </div>

                            {data.amount && data.limit && (
                                <div className="pt-3 border-t border-[var(--clr-primary-a0)]">
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm font-medium text-[var(--clr-primary-a0)]">
                                            Total Prize Pool:
                                        </span>
                                        <span className="text-lg font-bold text-[var(--clr-light-a0)]">
                                            ${calculatePrizePool()}
                                        </span>
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Wallet Info */}
                    <Card className="bg-[var(--clr-surface-a10)] border-[var(--clr-surface-a20)]">
                        <CardContent className="p-4">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    <DollarSign className="w-5 h-5 text-[var(--clr-primary-a0)]" />
                                    <div>
                                        <p className="text-sm text-[var(--clr-primary-a0)] font-medium">
                                            Your Balance
                                        </p>
                                        <p className="text-lg font-bold text-[var(--clr-light-a0)]">
                                            ${user.wallet.balance}
                                        </p>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <p className="text-xs text-[var(--clr-primary-a0)]">
                                        Required
                                    </p>
                                    <p className="text-sm font-medium text-[var(--clr-light-a0)]">
                                        ${data.amount || "0.00"}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Submit */}
                    <div className="space-y-3">
                        <Button
                            type="submit"
                            className="w-full bg-[var(--clr-primary-a0)] hover:bg-[var(--clr-primary-a10)] text-[var(--clr-light-a0)]"
                            disabled={processing || !data.name || !data.amount}
                        >
                            {processing ? "Creating..." : "Create Peer"}
                        </Button>

                        <p className="text-xs text-[var(--clr-surface-a50)] text-center">
                            You'll be charged ${data.amount || "0.00"} from your
                            wallet when you create this peer
                        </p>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
