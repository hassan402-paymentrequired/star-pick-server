import { useState } from "react";
import { Star, Minus, Plus, Users, Trophy, Target } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from "@/components/ui/sheet";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { cn } from "@/lib/utils";

interface Player {
    player_avatar: string;
    player_position: string;
    player_match_id: number;
    player_id: number;
    player_team: string;
    player_name: string;
    against_team_name: string;
    date: string;
    time: string;
}

interface SelectedPlayer extends Player {
    type: "main" | "sub";
}

interface FloatingBetSlipProps {
    selectedPlayers: SelectedPlayer[];
    onRemovePlayer: (playerId: number) => void;
    players: Array<{ star: number; players: Player[] }>;
}

export const FloatingBetSlip = ({
    selectedPlayers,
    onRemovePlayer,
    players,
}: FloatingBetSlipProps) => {
    const [isOpen, setIsOpen] = useState(false);

    const totalSelected = selectedPlayers.length;
    const mainPlayers = selectedPlayers.filter((p) => p.type === "main");
    const subPlayers = selectedPlayers.filter((p) => p.type === "sub");

    const getTierColor = (tier: number) => {
        switch (tier) {
            case 5:
                return "text-yellow-400";
            case 4:
                return "text-purple-400";
            case 3:
                return "text-blue-400";
            case 2:
                return "text-green-400";
            default:
                return "text-gray-400";
        }
    };

    const getTierBgColor = (tier: number) => {
        switch (tier) {
            case 5:
                return "bg-yellow-400/10 border-yellow-400/20";
            case 4:
                return "bg-purple-400/10 border-purple-400/20";
            case 3:
                return "bg-blue-400/10 border-blue-400/20";
            case 2:
                return "bg-green-400/10 border-green-400/20";
            default:
                return "bg-gray-400/10 border-gray-400/20";
        }
    };

    const getPlayerStarRating = (playerId: number) => {
        for (const group of players) {
            const player = group.players.find((p) => p.player_id === playerId);
            if (player) {
                return group.star;
            }
        }
        return 1;
    };

    const renderStars = (playerId: number) => {
        const tier = getPlayerStarRating(playerId);
        return Array.from({ length: 5 }, (_, i) => (
            <Star
                key={i}
                className={cn(
                    "h-3 w-3 transition-all duration-200",
                    i < tier ? getTierColor(tier) : "text-gray-400",
                    i < tier ? "fill-current" : ""
                )}
            />
        ));
    };

    // Group players by star rating
    const groupedPlayers = [5, 4, 3, 2, 1]
        .map((star) => {
            const mainPlayer = mainPlayers.find(
                (p) => getPlayerStarRating(p.player_id) === star
            );
            const subPlayer = subPlayers.find(
                (p) => getPlayerStarRating(p.player_id) === star
            );

            return {
                star,
                mainPlayer,
                subPlayer,
                hasPlayers: mainPlayer || subPlayer,
            };
        })
        .filter((group) => group.hasPlayers);

    if (totalSelected === 0) return null;

    return (
        <Sheet open={isOpen} onOpenChange={setIsOpen}>
            <SheetTrigger asChild>
                <Button
                    className="fixed bottom-20 left-1/2 transform -translate-x-1/2 h-16 w-16 rounded-full bg-gradient-to-r from-primary to-secondary shadow-2xl hover:shadow-3xl transition-all duration-300 border-2 border-white/20 z-50 group"
                    size="sm"
                >
                    <div className="flex flex-col items-center relative">
                        <div className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold animate-pulse">
                            {totalSelected}
                        </div>
                        <Trophy className="h-6 w-6 fill-current text-white group-hover:scale-110 transition-transform duration-200" />
                        <span className="text-xs font-bold text-white mt-1">
                            Team
                        </span>
                    </div>
                </Button>
            </SheetTrigger>

            <SheetContent
                side="bottom"
                className="h-[85vh] p-5  border-t border-primary"
            >
                <SheetHeader className="pb-6 border-b border-border/20">
                    <SheetTitle className="text-display flex items-center gap-3">
                        <div className="relative">
                            <Trophy className="h-8 w-8 text-neutral-400 fill-current" />
                            <div className="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                                {totalSelected}
                            </div>
                        </div>
                        <div>
                            <div className="text-2xl font-bold text-muted-white">
                                Your Star Team
                            </div>
                            <div className="text-sm text-muted-foreground">
                                {mainPlayers.length} Main • {subPlayers.length}{" "}
                                Subs
                            </div>
                        </div>
                    </SheetTitle>
                </SheetHeader>

                <div className="space-y-4 overflow-y-auto h-full pb-20">
                    {groupedPlayers.map((group) => (
                        <Card
                            key={group.star}
                            className={cn(
                                "border-2 transition-all duration-300 hover:shadow-lg",
                                getTierBgColor(group.star)
                            )}
                        >
                            <CardHeader className="pb-3">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="flex">
                                            {Array.from(
                                                { length: 5 },
                                                (_, i) => (
                                                    <Star
                                                        key={i}
                                                        className={cn(
                                                            "h-4 w-4 transition-all duration-200",
                                                            i < group.star
                                                                ? getTierColor(
                                                                      group.star
                                                                  )
                                                                : "text-gray-400",
                                                            i < group.star
                                                                ? "fill-current"
                                                                : ""
                                                        )}
                                                    />
                                                )
                                            )}
                                        </div>
                                        <span className="font-bold text-lg">
                                            {group.star}-Star Tier
                                        </span>
                                    </div>
                                    <Badge
                                        variant="outline"
                                        className={cn(
                                            "border-2",
                                            group.mainPlayer && group.subPlayer
                                                ? "bg-green-500/10 text-green-600 border-green-500/30"
                                                : "bg-orange-500/10 text-orange-600 border-orange-500/30"
                                        )}
                                    >
                                        {group.mainPlayer && group.subPlayer
                                            ? "Complete"
                                            : "Partial"}
                                    </Badge>
                                </div>
                            </CardHeader>

                            <CardContent className="space-y-3">
                                {/* Main Player */}
                                <div className="space-y-2">
                                    <div className="flex items-center gap-2">
                                        <Target className="h-4 w-4 text-primary" />
                                        <span className="font-semibold text-sm text-primary">
                                            Main Squad
                                        </span>
                                    </div>
                                    {group.mainPlayer ? (
                                        <div className="bg-primary/5 border border-primary/20 rounded-lg p-3 transition-all duration-200 hover:bg-primary/10">
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center gap-3 flex-1">
                                                    <div className="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                        {group.mainPlayer.player_name
                                                            .split(" ")
                                                            .map((n) => n[0])
                                                            .join("")
                                                            .toUpperCase()}
                                                    </div>
                                                    <div className="flex-1">
                                                        <div className="font-semibold text-foreground">
                                                            {
                                                                group.mainPlayer
                                                                    .player_name
                                                            }
                                                        </div>
                                                        <div className="text-xs text-muted-foreground flex items-center gap-2">
                                                            <span>
                                                                {
                                                                    group
                                                                        .mainPlayer
                                                                        .player_team
                                                                }
                                                            </span>
                                                            <span>•</span>
                                                            <span>
                                                                {
                                                                    group
                                                                        .mainPlayer
                                                                        .player_position
                                                                }
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() =>
                                                        onRemovePlayer(
                                                            group.mainPlayer!
                                                                .player_match_id
                                                        )
                                                    }
                                                    className="h-8 w-8 p-0 hover:bg-red-500/20 hover:text-red-500 transition-colors duration-200"
                                                >
                                                    <Minus className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="border-2 border-dashed border-muted-foreground/30 rounded-lg p-3 text-center">
                                            <span className="text-sm text-muted-foreground">
                                                No main player selected
                                            </span>
                                        </div>
                                    )}
                                </div>

                                {/* Sub Player */}
                                <div className="space-y-2">
                                    <div className="flex items-center gap-2">
                                        <Users className="h-4 w-4 text-secondary" />
                                        <span className="font-semibold text-sm text-secondary">
                                            Substitute
                                        </span>
                                    </div>
                                    {group.subPlayer ? (
                                        <div className="bg-secondary/5 border border-secondary/20 rounded-lg p-3 transition-all duration-200 hover:bg-secondary/10">
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center gap-3 flex-1">
                                                    <div className="w-10 h-10 rounded-full bg-gradient-to-br from-secondary to-primary flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                        {group.subPlayer.player_name
                                                            .split(" ")
                                                            .map((n) => n[0])
                                                            .join("")
                                                            .toUpperCase()}
                                                    </div>
                                                    <div className="flex-1">
                                                        <div className="font-semibold text-foreground">
                                                            {
                                                                group.subPlayer
                                                                    .player_name
                                                            }
                                                        </div>
                                                        <div className="text-xs text-muted-foreground flex items-center gap-2">
                                                            <span>
                                                                {
                                                                    group
                                                                        .subPlayer
                                                                        .player_team
                                                                }
                                                            </span>
                                                            <span>•</span>
                                                            <span>
                                                                {
                                                                    group
                                                                        .subPlayer
                                                                        .player_position
                                                                }
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() =>
                                                        onRemovePlayer(
                                                            group.subPlayer!
                                                                .player_match_id
                                                        )
                                                    }
                                                    className="h-8 w-8 p-0 hover:bg-red-500/20 hover:text-red-500 transition-colors duration-200"
                                                >
                                                    <Minus className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="border-2 border-dashed border-muted-foreground/30 rounded-lg p-3 text-center">
                                            <span className="text-sm text-muted-foreground">
                                                No substitute selected
                                            </span>
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    ))}

                    {/* Team Summary */}
                    {totalSelected > 0 && (
                        <Card className="bg-gradient-to-r from-primary/5 to-secondary/5 border-2 border-primary/20">
                            <CardContent className="p-4">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <div className="font-bold text-lg text-foreground">
                                            Team Summary
                                        </div>
                                        <div className="text-sm text-muted-foreground">
                                            {totalSelected}/10 players selected
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <div className="text-2xl font-bold text-primary">
                                            {Math.round(
                                                (totalSelected / 10) * 100
                                            )}
                                            %
                                        </div>
                                        <div className="text-xs text-muted-foreground">
                                            Complete
                                        </div>
                                    </div>
                                </div>

                                {/* Progress Bar */}
                                <div className="mt-3 w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        className="bg-gradient-to-r from-primary to-secondary h-2 rounded-full transition-all duration-500"
                                        style={{
                                            width: `${
                                                (totalSelected / 10) * 100
                                            }%`,
                                        }}
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </SheetContent>
        </Sheet>
    );
};
