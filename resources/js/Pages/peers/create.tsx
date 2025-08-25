import React, { useState } from "react";
import { Head, useForm, usePage } from "@inertiajs/react";
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
    LoaderIcon,
} from "lucide-react";
import { PageProps } from "@/types";
import { toast } from "sonner";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

interface CreatePeerProps extends PageProps {
    user: {
        wallet: {
            balance: string;
        };
    };
}

export default function CreatePeer({ user }: CreatePeerProps) {
    const { flash, errors: globalErrors } = usePage<{
        flash: { error: string; success: string };
    }>().props;
    const { data, setData, post, processing, errors } = useForm({
        name: "",
        amount: "",
        limit: "",
        sharing_ratio: "1",
        private: false,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (Number(user.wallet.balance) < Number(data.amount)) {
            toast.error("Insufficient balance. Please deposit to your wallet", {
                duration: 5000,
                position: "bottom-center",
            });
            return;
        }

        // console.log(data)

        post(route("peers.store"), {
            preserveScroll: true,
        });
    };

    const calculatePrizePool = () => {
        const amount = parseFloat(data.amount) || 0;
        const limit = parseInt(data.limit) || 0;
        return (amount * limit).toFixed(2);
    };

    // console.log(globalErrors)

    return (
        <MainLayout>
            <Head title="Create Peer" />

            <div className="space-y-4 p-5">
                {/* Header */}
                <div className="flex items-center gap-3">
                    <div>
                        <h1 className="text-xl font-bold text-muted">
                            Create Peer
                        </h1>
                        <p className="text-sm ">
                            Start a new group competition
                        </p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                    {/* Basic Info */}
                    <div className="p-1.5 border backdrop-blur-sm  rounded">
                        <Card className="bg-default/10  shadow-sm border-none rounded p-4 gap-3 border-border">
                            <CardHeader className="px-0">
                                <CardTitle className="flex items-center gap-2 text-muted">
                                    <Target className="w-5 h-5 text-[var(--clr-primary-a0)]" />
                                    Basic Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4 px-0">
                                <div className="gap-1 flex flex-col">
                                    <Label
                                        htmlFor="name"
                                        className="text-muted-white"
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
                                        className={`
                                        ₦{
                                            errors.name ? "border-red-500" : ""
                                        } text-muted-white
                                    `}
                                    />
                                    {errors.name && (
                                        <p className="text-sm text-red-600 mt-1">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="gap-1 flex flex-col">
                                    <Label
                                        htmlFor="amount"
                                        className="text-muted-white"
                                    >
                                        Entry Fee (₦)
                                    </Label>
                                    <Input
                                        id="amount"
                                        type="number"
                                        step="1"
                                        placeholder="0.00"
                                        value={data.amount}
                                        onChange={(e) =>
                                            setData("amount", e.target.value)
                                        }
                                        className={`₦{
                                        errors.amount ? "border-red-500" : ""
                                    } `}
                                    />
                                    {errors.amount && (
                                        <p className="text-sm text-red-600 mt-1">
                                            {errors.amount}
                                        </p>
                                    )}
                                </div>

                                <div className="gap-1 flex flex-col">
                                    <Label
                                        htmlFor="limit"
                                        className="text-muted-white"
                                    >
                                        Player Limit
                                    </Label>
                                    <Input
                                        id="limit"
                                        type="text"
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
                    </div>

                    {/* Settings */}
                    <div className="p-1.5 border backdrop-blur-sm  rounded">
                        <Card className="bg-default/10  shadow-sm border-none rounded p-4 gap-3 border-border">
                            {/* <Card className="bg-background rounded p-4 gap-3 border-border"> */}
                            <CardHeader className="p-0">
                                <CardTitle className="flex items-center gap-2 text-muted">
                                    <Info className="w-5 h-5 text-[var(--clr-primary-a0)]" />
                                    Settings
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4 p-0">
                                <div className="gap-1 flex flex-col">
                                    <Label
                                        htmlFor="sharing_ratio"
                                        className="text-muted-white"
                                    >
                                        Sharing Ratio
                                    </Label>
                                    <Select
                                        value={data.sharing_ratio}
                                        onValueChange={(e) =>
                                            setData("sharing_ratio", e)
                                        }
                                    >
                                        <SelectTrigger
                                            className={`${
                                                errors.sharing_ratio
                                                    ? "border-red-500"
                                                    : ""
                                            }  w-full text-muted-white`}
                                        >
                                            <SelectValue
                                                placeholder="Select"
                                                className="text-muted"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="1">
                                                100%
                                            </SelectItem>
                                            <SelectItem value="3">
                                                Spread
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p className="text-xs text-muted-foreground mt-1">
                                        How much of the prize pool the winner
                                        gets (1.0 = 100%)
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
                                        className="flex items-center gap-2 text-muted"
                                    >
                                        <Lock className="w-4 h-4" />
                                        Private Peer
                                    </Label>
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    Private peers are only visible to invited
                                    users
                                </p>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Preview */}
                    <div className="p-1.5 border backdrop-blur-sm  rounded">
                        <Card className="bg-default/10  shadow-sm border-none rounded p-4 gap-3 border-border">
                            <CardHeader className="p-0">
                                <CardTitle className="flex items-center gap-2 text-muted-white">
                                    <Globe className="w-5 h-5" />
                                    Peer Preview
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3 p-0">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-muted-white">
                                        Name:
                                    </span>
                                    <span className="font-medium text-muted">
                                        {data.name || "Your Peer Name"}
                                    </span>
                                </div>

                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-muted-white">
                                        Entry Fee:
                                    </span>
                                    <span className="font-medium text-muted">
                                        ₦{data.amount || "0.00"}
                                    </span>
                                </div>

                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-muted-white">
                                        Player Limit:
                                    </span>
                                    <span className="font-medium text-muted">
                                        {data.limit || "Unlimited"}
                                    </span>
                                </div>

                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-muted-white">
                                        Sharing Ratio:
                                    </span>
                                    <span className="font-medium text-muted">
                                        {data.sharing_ratio || "1"}x
                                    </span>
                                </div>

                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-muted-white">
                                        Privacy:
                                    </span>
                                    <Badge
                                        className={
                                            data.private
                                                ? "bg-red-100 text-red-800"
                                                : "bg-[var(--clr-primary-a0)] text-muted"
                                        }
                                    >
                                        {data.private ? "Private" : "Public"}
                                    </Badge>
                                </div>

                                {data.amount && data.limit && (
                                    <div className="pt-3 border-t border-[var(--clr-primary-a0)]">
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm font-medium text-muted">
                                                Total Prize Pool:
                                            </span>
                                            <span className="text-lg font-bold text-muted-white">
                                                ₦{calculatePrizePool()}
                                            </span>
                                        </div>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Wallet Info */}
                    <div className="p-1.5 border backdrop-blur-sm  rounded">
                        <Card className="bg-default/10  shadow-sm border-none rounded p-4 gap-3 border-border">
                            <CardContent className="p-4">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div>
                                            <p className="text-sm text-muted font-medium">
                                                Your Balance
                                            </p>
                                            <p className="text-lg font-bold text-muted-white">
                                                ₦{user.wallet.balance}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className="text-xs text-muted">
                                            Required
                                        </p>
                                        <p className="text-sm font-medium text-muted-white">
                                            ₦{data.amount || "0.00"}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                    {/* Submit */}
                    <div className="space-y-3">
                        <Button
                            type="submit"
                            className="w-full tracking-wider "
                            disabled={processing || !data.name || !data.amount}
                        >
                            {processing && (
                                <LoaderIcon className="animate-spin" />
                            )}
                            Create Peer
                        </Button>

                        <p className="text-xs text-muted-foreground text-center">
                            You'll be charged ₦{data.amount || "0.00"} from your
                            wallet when you create this peer
                        </p>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
