"use client"
 
import * as React from "react"
import { ThemeProvider as NextThemesProvider } from "next-themes"
import { type ThemeProviderProps } from "next-themes/dist/types"
import { Component } from "@/lib/component/component"
 
export const ThemeProvider: Component<ThemeProviderProps> = ({ children, ...props }) => {
  return <NextThemesProvider {...props}>{children}</NextThemesProvider>
}