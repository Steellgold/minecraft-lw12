"use client";

import { ArrowLeft } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar";
import { Button } from "./ui/button";
import { Card, CardContent, CardDescription, CardHeader } from "./ui/card";
import { Component } from "../component/component";
import { GState } from "@/app/page";
import { GameComponent } from "./game";
import { useMediaQuery } from "usehooks-ts";
import Image from "next/image";
import { OnlineStatus } from "./online-status";
import { ScrollArea } from "./ui/scroll-area";

type Props = {
  onBack: () => void;
  state: GState;
};

export const PlayerProfile: Component<Props> = ({ onBack, state }) => {
  const media = useMediaQuery('(min-width: 768px)');
  const hasActiveGame = state.games.some((game) => game.status === "STARTED");

  return (
    <div className="flex flex-col items-center justify-center mt-8">
      <Image src="/sized-title.png" width={350} height={100} alt="Supabase" className="mb-4" />

      <div className="flex-col sm:grid sm:grid-cols-2 sm:gap-4 max-w-4xl w-full space-y-4 sm:space-y-0 p-4 sm:p-0">
        <div className="max-w-2xl">
          <Card className="w-full">
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
                      <OnlineStatus username={state.username} />
                      &nbsp;- Player statistics
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-2">
                  <Button onClick={onBack} size="sm">
                    <ArrowLeft className="w-4 h-4 mr-2" />
                    Back
                  </Button>
                </div>
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
            </CardContent>
          </Card>

          <Card className="mt-4">
            <CardHeader>
              <h3 className="text-lg font-semibold text-foreground">Active game</h3>
            </CardHeader>

            <CardContent>
              {hasActiveGame ? (
                <>
                  {state.games.filter((game) => game.status === "STARTED").map((game) => (
                    <GameComponent key={game.gameId} game={game} onlyActive />
                  ))}
                </>
              ) : (
                <p className="text-muted-foreground">This player is not in any active game</p>
              )}
            </CardContent>
          </Card>
        </div>

        <Card className="max-w-lg w-full">
          <CardHeader>
            <h3 className="text-lg font-semibold text-foreground">Game history</h3>
          </CardHeader>

          <CardContent className="space-y-4">
            <div className="space-y-4">
              {!media ? (
                <ScrollArea className="h-72">
                  {state.games.filter((game) => game.status === "FINISHED").map((game) => (
                    <GameComponent game={game} key={game.gameId} />
                  ))}
                </ScrollArea>
              ) : (
                state.games.filter((game) => game.status === "FINISHED").map((game) => (
                  <GameComponent game={game} key={game.gameId} />
                ))
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
    

      {/* <Card className="max-w-lg w-full">
        <CardContent className="space-y-4">
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
      </Card> */}