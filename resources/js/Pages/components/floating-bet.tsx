import { useState } from "react";
import { Star, Minus, Plus } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from "@/components/ui/sheet";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent } from "@/components/ui/card";

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
                return "text-secondary";
            case 4:
                return "text-accent";
            case 3:
                return "text-primary";
            case 2:
                return "text-success";
            default:
                return "text-muted-foreground";
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
                className={`h-3 w-3 ${
                    i < tier ? getTierColor(tier) : "text-muted-foreground"
                } ${i < tier ? "fill-current" : ""}`}
            />
        ));
    };

    if (totalSelected === 0) return null;

    return (
        <Sheet open={isOpen} onOpenChange={setIsOpen}>
            <SheetTrigger asChild>
                <Button
                    className="fixed bottom-20 left-1/2 transform -translate-x-1/2 h-14 w-14 rounded-full gradient-primary shadow-floating floating-pulse border-2 border-primary-glow z-50"
                    size="sm"
                >
                    <div className="flex flex-col items-center">
                        <Star className="h-5 w-5 fill-current" />
                        <span className="text-xs font-bold">
                            {totalSelected}
                        </span>
                    </div>
                </Button>
            </SheetTrigger>

            <SheetContent side="bottom" className="h-[80vh] rounded-t-3xl">
                <SheetHeader className="pb-6">
                    <SheetTitle className="text-display flex items-center gap-2">
                        <Star className="h-6 w-6 text-secondary fill-current" />
                        Star Picks ({totalSelected}/10)
                    </SheetTitle>
                </SheetHeader>

                <div className="space-y-6 overflow-y-auto">
                    {/* Main Players */}
                    <div>
                        <h3 className="text-headline text-foreground mb-3 flex items-center gap-2">
                            Main Squad
                            <Badge variant="secondary">
                                {mainPlayers.length}/5
                            </Badge>
                        </h3>
                        <div className="space-y-2">
                            {mainPlayers.map((player) => (
                                <Card
                                    key={player.player_match_id}
                                    className="gradient-card border-border/40"
                                >
                                    <CardContent className="p-3">
                                        <div className="flex items-center justify-between">
                                            <div className="flex-1">
                                                <div className="flex items-center gap-2 mb-1">
                                                    <span className="font-semibold text-foreground">
                                                        {player.player_name}
                                                    </span>
                                                    <div className="flex">
                                                        {renderStars(
                                                            player.player_id
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="text-caption text-muted-foreground">
                                                    {player.player_team} •{" "}
                                                    {player.player_position}
                                                </div>
                                            </div>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() =>
                                                    onRemovePlayer(
                                                        player.player_match_id
                                                    )
                                                }
                                                className="h-8 w-8 p-0 hover:bg-destructive/20 hover:text-destructive"
                                            >
                                                <Minus className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}
                        </div>
                    </div>

                    {/* Sub Players */}
                    <div>
                        <h3 className="text-headline text-foreground mb-3 flex items-center gap-2">
                            Substitutes
                            <Badge variant="outline">
                                {subPlayers.length}/5
                            </Badge>
                        </h3>
                        <div className="space-y-2">
                            {subPlayers.map((player) => (
                                <Card
                                    key={player.player_match_id}
                                    className="gradient-card border-border/40"
                                >
                                    <CardContent className="p-3">
                                        <div className="flex items-center justify-between">
                                            <div className="flex-1">
                                                <div className="flex items-center gap-2 mb-1">
                                                    <span className="font-semibold text-foreground">
                                                        {player.player_name}
                                                    </span>
                                                    <div className="flex">
                                                        {renderStars(
                                                            player.player_id
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="text-caption text-muted-foreground">
                                                    {player.player_team} •{" "}
                                                    {player.player_position}
                                                </div>
                                            </div>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() =>
                                                    onRemovePlayer(
                                                        player.player_match_id
                                                    )
                                                }
                                                className="h-8 w-8 p-0 hover:bg-destructive/20 hover:text-destructive"
                                            >
                                                <Minus className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}
                        </div>
                    </div>
                </div>
            </SheetContent>
        </Sheet>
    );
};
