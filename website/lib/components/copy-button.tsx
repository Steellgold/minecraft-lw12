"use client";

import { useToast } from "@/hooks/use-toast";
import { Copy } from "lucide-react";

export const CopyButton = ({ text }: { text: string }) => {
  const { toast } = useToast()

  const copyToClipboard = async () => {
    toast({ title: "Copied to clipboard" });
    await navigator.clipboard.writeText(text);
  };

  return (
    <Copy onClick={copyToClipboard} className="w-3 h-3 ml-2 cursor-pointer" />
  );
}