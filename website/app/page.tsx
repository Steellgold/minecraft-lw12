"use client";

import { OnlineCount } from "@/lib/components/online-count";
import { Button } from "@/lib/components/ui/button";
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from "@/lib/components/ui/card";
import { Input } from "@/lib/components/ui/input";
import { ReactElement, useEffect, useState } from "react";
import { searchAction } from "./actions/player-search";
import { LoaderCircle } from "lucide-react";
import { useFormState, useFormStatus } from "react-dom";
import { Alert, AlertDescription, AlertTitle } from "@/lib/components/ui/alert";
import { PlayerProfile } from "@/lib/components/profile";
import Image from "next/image";
import { Team } from "@prisma/client";
import { JoinCard } from "@/lib/components/join-card";
import { Leaderboard } from "@/lib/components/top-players";

const initialState = {
  username: "",
  headUrl: "",
  isOnline: false,
  error: "",

  score: 0,
  deaths: 0,
  nbrGames: 0,

  games: [],
};

export type GState = {
  username: string;
  headUrl: string;
  isOnline: boolean;
  error: string;

  score: number;
  deaths: number;
  nbrGames: number;

  games: {
    gameId: string;
    simpleId: number;
    startedAt: string;
    status: "STARTED" | "FINISHED";
    players: {
      uuid: string;
      username: string;
      headUrl: string;
      score: number;
      deathCount: number;
      team: Team;
    }[];
    actions: {
      id: number;
      gameId: string;
      victimUuid: string;
      killerUuid: string;
      startedAt: string; // lmao, "startedAt" must be createdAt
    }[];
  }[];
};

const Home = () => {
  const [username, setUsername] = useState("");
  const [reset, setReset] = useState(false);

  const sendSearch = searchAction.bind(null, username);
  // @ts-expect-error - React DOM is not typed (i think)
  const [state, formAction] = useFormState<GState>(sendSearch, initialState);

  useEffect(() => {
    if (state.username) {
      document.title = `Player - ${state.username}`;
      setReset(false);
    }
  }, [state]);

  if (state.username && !reset) {
    return <PlayerProfile
      state={state}
      onBack={() => setReset(true)}
    />;
  }

  return (
    <div className="flex flex-col items-center justify-center mt-8">
      <Image src="/sized-title.png" width={350} height={100} alt="Supabase" className="mb-4" />

      <div className="flex-col sm:grid sm:grid-cols-2 sm:gap-4 max-w-3xl space-y-4 sm:space-y-0">
        <div>
          <Card className="w-full max-w-sm">
            <CardHeader>
              <CardDescription>
                Get the player statistics of the laser-games server
              </CardDescription>
            </CardHeader>
            <form action={formAction}>
              <CardContent className="grid gap-4 -mb-3">
                <Input
                  type="text"
                  placeholder="Username"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                />
              </CardContent>

              <CardFooter className="flex flex-col gap-4">
                <SearchButton />

                {state.error && (
                  <Alert variant={state.error ? "destructive" : "default"}>
                    <AlertTitle>{state.error}</AlertTitle>
                    <AlertDescription>
                      {state.error === "Player not found" && "The player you are looking for does not exist."}
                      {state.error === "Failed to fetch" && "An error occurred while fetching the player."}
                    </AlertDescription>
                  </Alert>
                )}
              </CardFooter>
            </form>

            <CardFooter className="-mt-4">
              <OnlineCount />
            </CardFooter>
          </Card>
          
          <JoinCard />
        </div>

        <Leaderboard />
      </div>
    </div>
  );
};

export default Home;

const SearchButton = (): ReactElement => {
  const { pending } = useFormStatus();

  return (
    <Button className="w-full" type="submit" disabled={pending}>
      {pending && <LoaderCircle className="w-5 h-5 mr-2 animate-spin" />}
      Search
    </Button>
  );
}