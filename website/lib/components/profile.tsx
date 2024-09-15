"use client";

import { ArrowLeft } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar";
import { Button } from "./ui/button";
import { Card, CardContent, CardDescription, CardHeader } from "./ui/card";
import { Component } from "../component/component";
import { GState } from "@/app/page";
import { GameComponent } from "./game";
import { Carousel, CarouselContent, CarouselItem, CarouselNext } from "./ui/carousel";
import { useMediaQuery } from "usehooks-ts";
import Image from "next/image";

export const PlayerProfile: Component<{ onBack: () => void, state: GState }> = ({ onBack, state }) => {
  const media = useMediaQuery('(min-width: 768px)');

  return (
    <div className="flex flex-col items-center justify-center mt-8">
      <Image src="/sized-title.png" width={350} height={100} alt="Supabase" className="mb-4" />

      <Card className="max-w-lg w-full">
        <CardHeader>
          <CardDescription className="flex items-center justify-between">
            <div className="flex items-center gap-2">
              <Avatar className="rounded-none">
                <AvatarImage src={state.headUrl} />
                <AvatarFallback>{state.username[0].toUpperCase()}</AvatarFallback>
              </Avatar>

              <div className="flex flex-col">
                <h2 className="text-lg font-semibold text-foreground">{state.username}</h2>
                <p className="text-sm text-foreground">
                  {state.isOnline ? (
                    <span className="text-primary">Online</span>
                  ) : (
                    <span className="text-destructive">Offline</span>
                  )}
                  &nbsp;- Player statistics
                </p>
              </div>
            </div>

            <Button onClick={onBack} size="sm">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back
            </Button>
          </CardDescription>
        </CardHeader>

        <CardContent className="space-y-4">
          <div className=" border border-primary rounded-md p-4">
            <div className="grid grid-cols-3 gap-4 text-center mb-4">
              <div>
                <p className="text-2xl font-bold">{state.score ?? 0}</p>
                <p className="text-sm text-primary">Score</p>
              </div>
              <div>
                <p className="text-2xl font-bold">{state.deaths ?? 0}</p>
                <p className="text-sm text-primary">Deaths</p>
              </div>
              <div>
                <p className="text-2xl font-bold">{state.nbrGames ?? 0}</p>
                <p className="text-sm text-primary">Games</p>
              </div>
            </div>
            
            <p className="text-sm text-center text-muted-foreground">Ratio K/D: {(state.score / state.deaths).toFixed(2)}</p>
          </div>

          <div className="space-y-4">
            {!media ? (
              <Carousel orientation="vertical" opts={{ align: "start", loop: true }}>
                <CarouselContent className="h-72 sm:h-[270px]">
                  {state.games.map((game) => (
                    <CarouselItem key={game.gameId}>
                      <GameComponent game={game} />
                    </CarouselItem>
                  ))}
                </CarouselContent>

                <CarouselNext />
              </Carousel>
            ) : (
              state.games.map((game) => (
                <GameComponent key={game.gameId} game={game} />
              ))
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  );
};