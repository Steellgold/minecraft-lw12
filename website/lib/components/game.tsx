"use client";

import { useEffect, useRef, useState } from "react";
import { supabase } from "../db/supabase";
import { dayJS } from "../day-js";
import { PlayerInGame } from "./player";
import { RealtimeChannel } from "@supabase/supabase-js";
import { cn } from "../utils";
import { Component } from "../component/component";

type Player = {
  uuid: string;
  username: string;
  headUrl: string;
  score: number;
  deaths: number;
  team: "RED" | "BLUE";
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
            // Handle score updates
            if (payload.eventType === "UPDATE" || payload.eventType === "INSERT") {
              const updatedPlayer = payload.new;
              setGameData((prev) => {
                const updatedPlayers = prev.players.map((player) => {
                  if (player.uuid === updatedPlayer.playerUuid) {
                    return {
                      ...player,
                      score: updatedPlayer.score,
                      deaths: updatedPlayer.deathCount,
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

  const totalPlayers = gameData.players.length;
  const maxScore = totalPlayers === 2 ? 20 : totalPlayers * 20;
  const redScore = gameData.players.filter((p) => p.team === "RED").reduce((sum, p) => sum + p.score, 0);
  const blueScore = gameData.players.filter((p) => p.team === "BLUE").reduce((sum, p) => sum + p.score, 0);
  const redPercentage = (redScore / maxScore) * 100;
  const bluePercentage = (blueScore / maxScore) * 100;

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
          <div className="w-full h-4 bg-gray-200 rounded-full overflow-hidden flex">
            <div
              className="bg-red-500 h-full"
              style={{ width: `${redPercentage}%` }}
            />
            <div
              className="bg-blue-500 h-full"
              style={{ width: `${bluePercentage}%` }}
            />
          </div>
          <div className="flex justify-between mt-2 text-sm">
            <span className="text-red-500">Red: {redScore}</span>
            
            <span className="text-muted-foreground">
              {elapsedTime}
            </span>

            <span className="text-blue-500">Blue: {blueScore}</span>
          </div>
        </div>
      )}
    </div>
  );
};