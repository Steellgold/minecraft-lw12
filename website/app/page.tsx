"use client";

import { OnlineCount } from "@/lib/components/online-count";
import { Button } from "@/lib/components/ui/button";
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from "@/lib/components/ui/card";
import { Input } from "@/lib/components/ui/input";
import { ReactElement, useState } from "react";
import { searchAction } from "./actions/player-search";
import { ArrowLeft, LoaderCircle } from "lucide-react";
import { useFormState, useFormStatus } from "react-dom";
import { Avatar, AvatarFallback, AvatarImage } from "@/lib/components/ui/avatar";
import { Alert, AlertDescription, AlertTitle } from "@/lib/components/ui/alert";

const initialState = {
  username: "",
  headUrl: "",
  isOnline: false,
  error: ""
};

const Home = () => {
  const [username, setUsername] = useState("");

  const sendSearch = searchAction.bind(null, username);
  // @ts-expect-error - React DOM is not typed (i think)
  const [state, formAction] = useFormState<typeof initialState>(sendSearch, initialState);

  const reset = () => {
    setUsername("");
    formAction();
  }

  if (state.username) {
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
                  <h2 className="text-lg font-semibold">{state.username}</h2>
                  <p className="text-sm text-gray-500">
                    {state.isOnline
                      ? <span className="text-primary">Online</span>
                      : <span className="text-destructive">Offline</span>
                    }&nbsp;
                    - Player statistics
                  </p>
                </div>
              </div>
            </CardDescription>
          </CardHeader>
          <CardFooter>
            <Button onClick={() => {
              reset();
              formAction();
            }} size="sm">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back
            </Button>
          </CardFooter>
        </Card>
      </div>
    );
  }

  return (
    <div className="flex items-center justify-center h-screen">
      <Card className="w-full max-w-sm">
        <CardHeader>
          <CardDescription>
            Get the player statistics of the laser-games server
          </CardDescription>
        </CardHeader>
        <form action={formAction}>
          <CardContent className="grid gap-4 -mb-3">
            <Input type="text" placeholder="Username" value={username} onChange={(e) => setUsername(e.target.value)} />
          </CardContent>

          <CardFooter className="flex flex-col gap-4">
            <SearchButton />

            {state.error && (
              <Alert variant={state.error ? "destructive" : "default"}>
                <AlertTitle>
                  {state.error}
                </AlertTitle>
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
    </div>
  );
}

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