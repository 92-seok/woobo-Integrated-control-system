declare module 'node-rtsp-stream' {
  interface StreamOptions {
    name: string;
    streamUrl: string;
    wsPort: number;
    width?: number;
    height?: number;
    ffmpegPath?: string;
    ffmpegOptions?: Record<string, any>;
  }

  class Stream {
    constructor(options: StreamOptions);
    stream: any;
    mpeg1Muxer: any;
    restartStream(): void;
    stop(): void;
    startMpeg1Stream(): void;
    pipeStreamToSocketServer(): void;
  }

  export = Stream;
} 