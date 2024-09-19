"use client";

import { useEffect, useRef, useState } from "react";
import { supabase } from "../db/supabase";
import { dayJS } from "../day-js";
import { PlayerInGame } from "./player";
import { RealtimeChannel } from "@supabase/supabase-js";
import { cn } from "../utils";
import { Component } from "../component/component";
import { Team } from "@prisma/client";
import { Bar, BarChart, CartesianGrid, XAxis } from "recharts"
import { ChartConfig, ChartContainer, ChartTooltip, ChartTooltipContent } from "@/lib/components/ui/chart"

type Player = {
  uuid: string;
  username: string;
  headUrl: string;
  score: number;
  deathCount: number;
  team: Team;
};

type GameData = {
  gameId: string;
  simpleId: number;
  startedAt: string;
  status: "STARTED" | "FINISHED";
  players: Player[];
};

type GameComponentProps = {
  game: GameData;
};

export const GameComponent: Component<GameComponentProps> = ({ game }) => {
  const [gameData, setGameData] = useState(game);
  const channelRef = useRef<RealtimeChannel | null>(null);
  const [elapsedTime, setElapsedTime] = useState("");

  useEffect(() => {
    if (gameData.status === "STARTED") {
      channelRef.current = supabase
        .channel(`game_${gameData.gameId}`)
        .on(
          "postgres_changes",
          {
            event: "*",
            schema: "public",
            table: "Game",
            filter: `id=eq.${gameData.gameId}`,
          },
          (payload) => {
            if (payload.eventType === "UPDATE") {
              setGameData((prev) => ({ ...prev, ...payload.new }));
            }
          }
        )
        .on(
          "postgres_changes",
          {
            event: "*",
            schema: "public",
            table: "Score",
            filter: `gameId=eq.${gameData.gameId}`,
          },
          (payload) => {
            if (payload.eventType === "UPDATE" || payload.eventType === "INSERT") {
              const updatedPlayer = payload.new;
              setGameData((prev) => {
                const updatedPlayers = prev.players.map((player) => {
                  if (player.uuid === updatedPlayer.playerUuid) {
                    return {
                      ...player,
                      score: updatedPlayer.score,
                      deathCount: updatedPlayer.deathCount,
                    };
                  }
                  return player;
                });
                return {
                  ...prev,
                  players: updatedPlayers,
                };
              });
            }
          }
        )
        .subscribe();
    }

    const intervalId = setInterval(() => {
      const now = dayJS();
      const startedAt = dayJS(gameData.startedAt);
      const diffInSeconds = now.diff(startedAt, 'second');

      const minutes = Math.floor(diffInSeconds / 60);
      const seconds = diffInSeconds % 60;

      const hours = Math.floor(minutes / 60);
      const minutesLeft = minutes % 60;
      if (hours > 0) {
        setElapsedTime(`${hours}h ${minutesLeft}m ${seconds}s`);
      } else {
        setElapsedTime(`${minutesLeft}m ${seconds}s`);
      }
    }, 1000);

    return () => {
      if (channelRef.current) {
        supabase.removeChannel(channelRef.current);
        channelRef.current = null;
      }

      clearInterval(intervalId);
    };
  }, [gameData.status, gameData.gameId, gameData.startedAt]);

  useEffect(() => {
    if (gameData.status === "FINISHED" && channelRef.current) {
      supabase.removeChannel(channelRef.current);
      channelRef.current = null;
    }
  }, [gameData.status]);

  const teamScores = gameData.players.reduce((acc, player) => {
    if (!acc[player.team]) {
      acc[player.team] = 0;
    }
    acc[player.team] += player.score;
    return acc;
  }, {} as Record<string, number>);

  const chartConfig = {
    RED: {
      color: "#FF4444",
      label: "Red Team",
    },
    BLUE: {
      color: "#4444FF",
      label: "Blue Team",
    },
    YELLOW: {
      color: "#FFD744",
      label: "Yellow Team",
    },
    GREEN: {
      color: "#008444",
      label: "Green Team",
    },
    PURPLE: {
      color: "#844484",
      label: "Purple Team",
    },
    ORANGE: {
      color: "#FFA544",
      label: "Orange Team",
    },
    PINK: {
      color: "#FFC4CB",
      label: "Pink Team",
    },
    WHITE: {
      color: "#FFFFFF",
      label: "White Team",
    }
  } satisfies ChartConfig;

  const chartData = Object.keys(teamScores).map((team) => ({
    team,
    score: teamScores[team],
    fill: chartConfig[team as keyof typeof chartConfig].color,
  }));

  return (
    <div key={gameData.gameId} className={cn("border rounded-md p-4", {
      "border-primary": gameData.status === "STARTED",
      "border-muted-foreground": gameData.status === "FINISHED",
    })}>
      <div className="flex items-center justify-between mb-2">
        <div className="flex flex-col -space-y-1">
          <h4 className="text-lg font-semibold text-foreground">Game #{gameData.simpleId.toString().padStart(4, "0")}</h4>
          <p className="text-sm text-muted-foreground">
            {dayJS(gameData.startedAt).format("DD/MM/YYYY HH:mm")}
          </p>
        </div>

        <p className="text-sm text-muted-foreground">
          {gameData.status === "FINISHED"
            ? "Finished"
            : <>
                <span className="h-2 w-2 bg-green-700 rounded-full inline-block animate-pulse duration-1000" />
                &nbsp;Real-time updates
              </>
          }
        </p>
      </div>

      <div className="flex flex-col">
        {gameData.players.map((player) => (
          <PlayerInGame key={player.uuid} player={player} />
        ))}
      </div>

      {gameData.status === "STARTED" && (
        <div className="mt-4">
          <ChartContainer config={chartConfig}>
            <BarChart
              accessibilityLayer
              data={chartData}
              margin={{
                top: 20,
              }}
            >
              <CartesianGrid vertical={false} />

              <XAxis
                dataKey="team"
                tickLine={false}
                tickMargin={10}
                axisLine={false}
                tickFormatter={(value) => chartConfig[value as keyof typeof chartConfig].label}
              />

              <ChartTooltip
                cursor={false}
                content={<ChartTooltipContent hideLabel />}
              />
              
              <Bar
                dataKey="score"
                radius={8}
                label={{ position: "top" }}
              />
            </BarChart>
          </ChartContainer>

          <div className="flex justify-between mt-2 text-xs">
            <span className="text-muted-foreground">
              Time elapsed:&nbsp;
              {elapsedTime}
            </span>
          </div>
        </div>
      )}
    </div>
  );
};