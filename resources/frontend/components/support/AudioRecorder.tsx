import { useState, useRef, useCallback } from 'react';
import { FaMicrophone, FaStop, FaPlay, FaTrash, FaPause } from 'react-icons/fa';
import { translate } from '../../utils/translate';

interface AudioRecorderProps {
    onAudioRecorded: (audioBlob: Blob | null) => void;
    maxDuration?: number; // in seconds
}

const AudioRecorder = ({ onAudioRecorded, maxDuration = 300 }: AudioRecorderProps) => {
    const [isRecording, setIsRecording] = useState(false);
    const [isPaused, setIsPaused] = useState(false);
    const [recordingTime, setRecordingTime] = useState(0);
    const [audioBlob, setAudioBlob] = useState<Blob | null>(null);
    const [audioUrl, setAudioUrl] = useState<string | null>(null);
    const [isPlaying, setIsPlaying] = useState(false);

    const mediaRecorderRef = useRef<MediaRecorder | null>(null);
    const audioChunksRef = useRef<Blob[]>([]);
    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);
    const audioElementRef = useRef<HTMLAudioElement | null>(null);

    const formatTime = (seconds: number): string => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    const startRecording = useCallback(async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const mediaRecorder = new MediaRecorder(stream);
            mediaRecorderRef.current = mediaRecorder;
            audioChunksRef.current = [];

            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    audioChunksRef.current.push(event.data);
                }
            };

            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunksRef.current, { type: 'audio/webm' });
                const audioUrl = URL.createObjectURL(audioBlob);
                setAudioBlob(audioBlob);
                setAudioUrl(audioUrl);
                onAudioRecorded(audioBlob);

                // Stop all tracks
                stream.getTracks().forEach(track => track.stop());
            };

            mediaRecorder.start(1000); // Collect data every second
            setIsRecording(true);
            setIsPaused(false);

            // Start timer
            timerRef.current = setInterval(() => {
                setRecordingTime((prev) => {
                    if (prev >= maxDuration) {
                        stopRecording();
                        return prev;
                    }
                    return prev + 1;
                });
            }, 1000);
        } catch (error) {
            console.error('Error accessing microphone:', error);
            alert(translate('Could not access microphone. Please ensure you have granted permission.'));
        }
    }, [maxDuration, onAudioRecorded]);

    const pauseRecording = useCallback(() => {
        if (mediaRecorderRef.current && isRecording) {
            mediaRecorderRef.current.pause();
            setIsPaused(true);
            if (timerRef.current) {
                clearInterval(timerRef.current);
            }
        }
    }, [isRecording]);

    const resumeRecording = useCallback(() => {
        if (mediaRecorderRef.current && isPaused) {
            mediaRecorderRef.current.resume();
            setIsPaused(false);
            timerRef.current = setInterval(() => {
                setRecordingTime((prev) => {
                    if (prev >= maxDuration) {
                        stopRecording();
                        return prev;
                    }
                    return prev + 1;
                });
            }, 1000);
        }
    }, [isPaused, maxDuration]);

    const stopRecording = useCallback(() => {
        if (mediaRecorderRef.current && isRecording) {
            mediaRecorderRef.current.stop();
            setIsRecording(false);
            setIsPaused(false);
            if (timerRef.current) {
                clearInterval(timerRef.current);
            }
        }
    }, [isRecording]);

    const deleteRecording = useCallback(() => {
        if (audioUrl) {
            URL.revokeObjectURL(audioUrl);
        }
        setAudioBlob(null);
        setAudioUrl(null);
        setRecordingTime(0);
        setIsPlaying(false);
        onAudioRecorded(null);

        if (audioElementRef.current) {
            audioElementRef.current.pause();
            audioElementRef.current = null;
        }
    }, [audioUrl, onAudioRecorded]);

    const togglePlayback = useCallback(() => {
        if (audioUrl) {
            if (!audioElementRef.current) {
                audioElementRef.current = new Audio(audioUrl);
                audioElementRef.current.onended = () => setIsPlaying(false);
            }

            if (isPlaying) {
                audioElementRef.current.pause();
                setIsPlaying(false);
            } else {
                audioElementRef.current.play();
                setIsPlaying(true);
            }
        }
    }, [audioUrl, isPlaying]);

    return (
        <div className="w-full">
            {!audioBlob ? (
                <div className="flex flex-col items-center gap-4 p-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                    {!isRecording ? (
                        <>
                            <button
                                type="button"
                                onClick={startRecording}
                                className="w-16 h-16 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors shadow-lg"
                                title={translate('Start Recording')}
                            >
                                <FaMicrophone className="w-6 h-6" />
                            </button>
                            <p className="text-sm text-gray-600">{translate('Click to record audio message')}</p>
                        </>
                    ) : (
                        <>
                            <div className="flex items-center gap-4">
                                <div className="flex items-center gap-2">
                                    <div className={`w-3 h-3 rounded-full ${isPaused ? 'bg-yellow-500' : 'bg-red-500 animate-pulse'}`} />
                                    <span className="text-lg font-mono font-semibold">{formatTime(recordingTime)}</span>
                                </div>
                                <span className="text-sm text-gray-500">/ {formatTime(maxDuration)}</span>
                            </div>
                            <div className="flex items-center gap-3">
                                {isPaused ? (
                                    <button
                                        type="button"
                                        onClick={resumeRecording}
                                        className="w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white hover:bg-green-600 transition-colors"
                                        title={translate('Resume')}
                                    >
                                        <FaMicrophone className="w-5 h-5" />
                                    </button>
                                ) : (
                                    <button
                                        type="button"
                                        onClick={pauseRecording}
                                        className="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-500 text-white hover:bg-yellow-600 transition-colors"
                                        title={translate('Pause')}
                                    >
                                        <FaPause className="w-5 h-5" />
                                    </button>
                                )}
                                <button
                                    type="button"
                                    onClick={stopRecording}
                                    className="w-12 h-12 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors"
                                    title={translate('Stop')}
                                >
                                    <FaStop className="w-5 h-5" />
                                </button>
                            </div>
                            <p className="text-xs text-gray-500">
                                {isPaused ? translate('Recording paused') : translate('Recording...')}
                            </p>
                        </>
                    )}
                </div>
            ) : (
                <div className="flex items-center gap-4 p-4 border border-gray-200 rounded-lg bg-white">
                    <button
                        type="button"
                        onClick={togglePlayback}
                        className="w-10 h-10 flex items-center justify-center rounded-full bg-primary text-white hover:bg-primary/90 transition-colors"
                    >
                        {isPlaying ? <FaPause className="w-4 h-4" /> : <FaPlay className="w-4 h-4 ml-0.5" />}
                    </button>
                    
                    <div className="flex-1">
                        <p className="text-sm font-medium">{translate('Audio recorded')}</p>
                        <p className="text-xs text-gray-500">{formatTime(recordingTime)}</p>
                    </div>
                    
                    <button
                        type="button"
                        onClick={deleteRecording}
                        className="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                        title={translate('Delete recording')}
                    >
                        <FaTrash className="w-4 h-4" />
                    </button>
                </div>
            )}
        </div>
    );
};

export default AudioRecorder;