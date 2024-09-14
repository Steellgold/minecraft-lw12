import { ArrowLeft } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar";
import { Button } from "./ui/button";
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from "./ui/card";
import { Component } from "../component/component";
import { GState } from "@/app/page";
import { GameComponent } from "./game";

export const PlayerProfile: Component<{ onBack: () => void, state: GState }> = ({ onBack, state }) => {
  return (
    <div className="flex items-center justify-center h-screen">
      <Card className="w-full max-w-md">
        <CardHeader>
          <CardDescription>
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
          </CardDescription>
        </CardHeader>

        <CardContent className="space-y-4">
          <div className="grid grid-cols-3 gap-4 text-center border border-primary rounded-md p-4">
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
          <div className="mt-4">
            <p className="text-sm text-center text-muted-foreground">
              Ratio K/D: {(state.score / state.deaths).toFixed(2)}
            </p>
          </div>

          <div>
            <h3 className="text-lg font-semibold text-foreground mb-1">Last games</h3>
            <div className="grid gap-4">
              {state.games.map((game) => (
                <GameComponent key={game.gameId} game={game} />
              ))}
            </div>
          </div>
        </CardContent>
        <CardFooter>
          <Button onClick={onBack} size="sm">
            <ArrowLeft className="w-4 h-4 mr-2" />
            Back
          </Button>
        </CardFooter>
      </Card>
    </div>
  );
};