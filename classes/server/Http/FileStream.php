<?php

namespace local_oidcserver\psr\http;

use Psr\Http\Message\StreamInterface;

/**
 * Implements a stream interface around a simple php string.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 */
class FileStream implements StreamInterface
{

    protected $filepath;

    protected $FILE;

    public function __construct($filepath) {
        $this->filepath = $filepath;
        $this->FILE = fopen($filepath, 'r+');
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString() {
        $content = file($this->filepath);
        return implode('', $content);
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close() {
        // Frees memory.
        fclose($this->FILE);
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach() {
        $resource = $this->FILE;
        $this->FILE = null; // detach from object instance.
        return $resource; // Give the detached resource back to caller.
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize() {
        $stats = stat($this->filepath);
        return $stat[7];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell() {
        return ftell($this->FILE);
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof() {
        if (!is_null($this->FILE)) {
            return feof($this->FILE);
        }
        return false;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable() {
        if (!is_null($this->FILE)) {
            return true;
        }
        return false;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET) {
        return fseek($this->FILE, $offset, $whence);
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind() {
        if (!is_null($this->FILE)) {
            return fseek($this->FILE, 0);
        }
        throw new RuntimeException("Unattached stream resource");
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable() {
        if (!is_null($this->FILE)) {
            return true;
        }
        return false;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string) {
        if ($this->isWritable()) {
            fwrite($this->FILE, $string);
        }
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable() {
        if (!is_null($this->FILE)) {
            return true;
        }
        return null;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length) {
        $return = fread($this->FILE, $length);
        return $return;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents() {
        $return = fread($this->FILE, $this->getSize());
        return $return;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null) {
        $mtd = ['storage' => 'file', 'filepath' => $this->filepath, 'class' => 'FileStream', 'size' => $this->getSize()];
        if (is_null($key)) {
            return $mtd;
        }
        if (array_key_exists($key, array_keys($mtd))) {
            return $mtd[$key];
        }
        return null;
    }
}
