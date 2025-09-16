// Generated TypeScript definitions for BaseApi
// Do not edit manually - regenerate with: php bin/console types:generate

export type UUID = string;
export type Envelope<T> = { data: T };

export interface ErrorResponse {
  error: string;
  requestId: string;
  errors?: Record<string, string>;
}

export type GetHealthResponse = Envelope<any>;

export type PostHealthResponse = Envelope<any>;

export type PostSignupResponse = Envelope<{ user: any[] }>;

export type PostLoginResponse = Envelope<{ user: any[] }>;

export type PostLogoutResponse = Envelope<{ message: string }>;

export type GetMeResponse = Envelope<{ user: any[] }>;
