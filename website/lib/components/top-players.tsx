"use client";

import { useEffect, useState } from "react";
import { Card, CardContent, CardDescription, CardHeader } from "@/lib/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/lib/components/ui/avatar";
import { ScrollArea } from "@/lib/components/ui/scroll-area"
import { cn } from "../utils";

interface Player {
  username: string;
  headUrl: string | null;
  totalScore: number;
  totalDeaths: number;
}

export const Leaderboard = () => {
  const [players, setPlayers] = useState<Player[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchPlayers = async () => {
      try {
        const response = await fetch("/api/top", {
          headers: {
            "Cache-Control": "no-cache"
          }
        });
        const data = await response.json();
        const sortedPlayers = data.sort((a: Player, b: Player) => b.totalScore - a.totalScore);
        setPlayers(sortedPlayers);
      } catch (error) {
        console.error("Failed to fetch players:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchPlayers();
  }, []);

  if (loading) {
    return <Card>
      <CardHeader>
        <CardDescription>Top Players by Score</CardDescription>
      </CardHeader>
      <CardContent>
        <p>Loading...</p>
      </CardContent>
    </Card>;
  }

  return (
    <div className="h-full">
      <Card className="h-full">
        <CardHeader>
          <CardDescription>Top Players by Score</CardDescription>
        </CardHeader>
        <CardContent className="h-full">
          <ScrollArea className="max-h-max h-full">
            {players.length === 0 ? (
              <p>No players found</p>
            ) : (
              <ul className="space-y-4">
                {players.map((player, index) => (
                  <li key={index} className="flex items-center gap-4">
                    <div className="relative">
                      {index < 3 && (
                        <div className="absolute -bottom-[19.5px] left-2.5 w-6 h-6 rounded-full flex z-10">
                          {index == 0 ? <>🥇</> : index == 1 ? <>🥈</> : index == 2 ? <>🥉</> : null}
                        </div>
                      )}

                      <Avatar className={cn("rounded-none")}>
                        <AvatarImage src={player.headUrl || "/default-avatar.png"} />
                        <AvatarFallback>{player.username[0].toUpperCase()}</AvatarFallback>
                      </Avatar>
                    </div>
                    <div className="flex flex-col">
                      <span className="text-lg font-semibold">{player.username}</span>
                      <span className="text-sm text-muted-foreground">
                        Score: {player.totalScore} | Deaths: {player.totalDeaths}
                      </span>
                    </div>
                  </li>
                ))}
              </ul>
            )}
          </ScrollArea>
        </CardContent>
      </Card>
    </div>
  );
};
